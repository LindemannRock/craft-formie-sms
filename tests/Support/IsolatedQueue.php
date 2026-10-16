<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Support;

use craft\queue\Queue;

/**
 * Connection-local shadows keep bootstrap and dispatch off owner state.
 * @since 3.11.3
 */
final class IsolatedQueue extends Queue
{
    public function init(): void
    {
        parent::init();
        if ($this->db->getDriverName() !== 'mysql') {
            throw new \RuntimeException('Formie SMS transport tests require MySQL.');
        }
        foreach ([
            '{{%queue}}', '{{%smsmanager_logs}}', '{{%smsmanager_analytics}}',
            '{{%smsmanager_transportguards}}', '{{%smsmanager_transportreleases}}',
            '{{%smsmanager_settings}}',
            '{{%smsmanager_providers}}', '{{%smsmanager_senderids}}',
        ] as $table) {
            $original = $this->db->quoteTableName($this->db->getSchema()->getRawTableName($table));
            $temporary = $this->db->quoteTableName('formie_sms_test_' . bin2hex(random_bytes(8)));
            $this->db->createCommand("CREATE TEMPORARY TABLE {$temporary} LIKE {$original}")->execute();
            if (in_array($table, ['{{%smsmanager_settings}}', '{{%smsmanager_providers}}', '{{%smsmanager_senderids}}'], true)) {
                $this->db->createCommand("INSERT INTO {$temporary} SELECT * FROM {$original}")->execute();
            }
            $this->db->createCommand("ALTER TABLE {$temporary} RENAME TO {$original}")->execute();
            // MySQL drops these exact connection-owned tables even on process failure.
            $this->db->getSchema()->refreshTableSchema($table);
        }
    }

    public function execute($id, $message, $ttr, $attempt, $workerPid): bool
    {
        fwrite(STDERR, "Queue workers are forbidden in Formie SMS tests.\n");
        exit(91);
    }
}
