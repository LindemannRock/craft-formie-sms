<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\console\controllers;

use lindemannrock\base\console\controllers\AbstractHelpController;

/**
 * Console help for Formie SMS commands.
 *
 * @since 3.11.0
 */
final class HelpController extends AbstractHelpController
{
    /**
     * @inheritdoc
     */
    protected function helpManifest(): array
    {
        return [
            'title' => 'Formie SMS',
            'pluginHandle' => 'formie-sms',
            'commandPrefixes' => [
                'php craft',
                'ddev craft',
            ],
            'summary' => 'Use these commands to migrate older Formie SMS integration settings to handle-based SMS Manager sender IDs.',
            'common' => [
                'migrate/integration-handles',
            ],
            'groups' => [
                [
                    'name' => 'migrate',
                    'label' => 'Migrations',
                    'description' => 'Update legacy Formie SMS integration data.',
                    'commands' => [
                        [
                            'path' => 'migrate/integration-handles',
                            'summary' => 'Populate senderIdHandle for legacy integrations.',
                            'description' => 'Scan Formie forms for SMS integration blocks that still have the legacy senderIdId field and add senderIdHandle when the matching SMS Manager sender still exists.',
                            'examples' => [
                                'formie-sms/migrate/integration-handles',
                            ],
                            'notes' => [
                                'The command is idempotent; re-running it is safe once forms are current.',
                                'Legacy senderIdId and providerId values are left in place for rollback safety.',
                                'Dangling senderIdId values are reported as unresolved and must be fixed by re-picking a sender in the Formie UI.',
                                'The command updates Formie form settings directly and does not trigger Formie save hooks.',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
