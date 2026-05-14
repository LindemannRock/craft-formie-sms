<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\console\controllers;

use Craft;
use craft\console\Controller;
use craft\db\Query;
use lindemannrock\smsmanager\SmsManager;
use Throwable;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Migrate Controller
 *
 * Console commands for migrating formie-sms data between schema versions.
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     3.10.0
 */
class MigrateController extends Controller
{
    /**
     * Populate `senderIdHandle` on Formie SMS integrations that still rely
     * on the legacy `senderIdId` int field (forms saved pre-3.10).
     *
     * For each Formie form whose `settings.integrations.<handle>` block
     * carries `senderIdId` but lacks (or has an empty) `senderIdHandle`:
     *   1. Look up the matching `SenderIdRecord` by id.
     *   2. If found and the record has a non-empty handle, set
     *      `senderIdHandle` on the block.
     *   3. Save the form via a direct DB update (no Formie save hooks,
     *      no side effects).
     *
     * Idempotent — re-running the command is a no-op once every block
     * already has `senderIdHandle` set. The legacy `senderIdId` /
     * `providerId` fields are left in place by design; their removal
     * lands in a later release after the rollback window closes (see
     * `plugins/formie-sms/.internal/todo.md` Stage 1f).
     *
     * Dangling references (`senderIdId` points at a deleted record)
     * are reported in the output; admin needs to re-pick the sender via
     * the Formie UI for those forms.
     *
     * @return int Process exit code. `0` on success, non-zero when any
     *             form errored during DB update.
     */
    public function actionIntegrationHandles(): int
    {
        $this->stdout("Scanning Formie forms for SMS integrations with legacy `senderIdId` field…\n");

        // Pre-filter at SQL level. Any form whose settings JSON mentions
        // `"senderIdId":` could be a candidate — the PHP walk below
        // confirms which integration blocks actually need migration.
        $rows = (new Query())
            ->select(['id', 'settings'])
            ->from('{{%formie_forms}}')
            ->andWhere(['like', 'settings', '"senderIdId":'])
            ->all();

        if (empty($rows)) {
            $this->stdout("No forms with legacy senderIdId fields found. Nothing to migrate.\n", Console::FG_GREEN);
            return ExitCode::OK;
        }

        $this->stdout(sprintf("Found %d candidate form(s).\n\n", count($rows)));

        $stats = ['migrated' => 0, 'already_current' => 0, 'unresolved' => 0, 'errored' => 0];

        foreach ($rows as $row) {
            $result = $this->migrateOne((int) $row['id'], (string) $row['settings']);
            $stats[$result['status']]++;
            if (!empty($result['details'])) {
                $color = match ($result['status']) {
                    'migrated' => Console::FG_GREEN,
                    'unresolved' => Console::FG_YELLOW,
                    'errored' => Console::FG_RED,
                    default => null,
                };
                $line = sprintf("  [form %d] %s\n", $row['id'], $result['details']);
                if ($color !== null) {
                    $this->stdout($line, $color);
                } else {
                    $this->stdout($line);
                }
            }
        }

        $summaryColor = ($stats['errored'] > 0)
            ? Console::FG_RED
            : (($stats['unresolved'] > 0) ? Console::FG_YELLOW : Console::FG_GREEN);

        $this->stdout(sprintf(
            "\nDone. Migrated: %d, Already current: %d, Unresolved (dangling): %d, Errored: %d\n",
            $stats['migrated'],
            $stats['already_current'],
            $stats['unresolved'],
            $stats['errored']
        ), $summaryColor);

        return ($stats['errored'] > 0) ? ExitCode::SOFTWARE : ExitCode::OK;
    }

    /**
     * Migrate one form's settings JSON.
     *
     * @return array{status: 'migrated'|'already_current'|'unresolved'|'errored', details: string|null}
     */
    private function migrateOne(int $formId, string $settingsJson): array
    {
        try {
            $settings = json_decode($settingsJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            return ['status' => 'errored', 'details' => 'JSON decode failed: ' . $e->getMessage()];
        }

        if (!is_array($settings) || empty($settings['integrations']) || !is_array($settings['integrations'])) {
            return ['status' => 'already_current', 'details' => null];
        }

        $modified = false;
        $unresolvedIds = [];
        $migratedBlocks = [];

        foreach ($settings['integrations'] as $blockHandle => &$block) {
            if (!is_array($block)) {
                continue;
            }

            // Block must look like an SMS integration block (has
            // `senderIdId` key). Other Formie integration shapes don't
            // share this field name.
            if (!array_key_exists('senderIdId', $block)) {
                continue;
            }

            // Already migrated — skip silently.
            if (!empty($block['senderIdHandle'])) {
                continue;
            }

            $senderIdId = $block['senderIdId'] ?? null;
            if (empty($senderIdId)) {
                // Block has an explicit null/empty senderIdId. Nothing
                // we can resolve from — treat as already-current; the
                // dispatch-time `resolveSenderIdHandle()` will fall back
                // to SMS Manager's default sender.
                continue;
            }

            $record = SmsManager::$plugin->senderIds->getSenderIdById((int) $senderIdId);
            if (!$record || empty($record->handle)) {
                $unresolvedIds[] = (int) $senderIdId;
                continue;
            }

            $block['senderIdHandle'] = (string) $record->handle;
            $modified = true;
            $migratedBlocks[] = sprintf(
                '"%s" (senderIdId=%d → senderIdHandle="%s")',
                $blockHandle,
                (int) $senderIdId,
                $record->handle
            );
        }
        unset($block);

        if ($modified) {
            try {
                $newJson = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                return ['status' => 'errored', 'details' => 'JSON re-encode failed: ' . $e->getMessage()];
            }

            try {
                Craft::$app->getDb()->createCommand()->update(
                    '{{%formie_forms}}',
                    ['settings' => $newJson],
                    ['id' => $formId]
                )->execute();
            } catch (Throwable $e) {
                return ['status' => 'errored', 'details' => 'DB update failed: ' . $e->getMessage()];
            }

            return [
                'status' => 'migrated',
                'details' => sprintf('Updated %d block(s): %s', count($migratedBlocks), implode(', ', $migratedBlocks)),
            ];
        }

        if (!empty($unresolvedIds)) {
            return [
                'status' => 'unresolved',
                'details' => sprintf(
                    'Dangling senderIdId(s): %s — admin needs to re-pick a sender via the Formie UI.',
                    implode(', ', array_unique($unresolvedIds))
                ),
            ];
        }

        return ['status' => 'already_current', 'details' => null];
    }
}
