<?php
/**
 * Formie SMS translation file (Japanese)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'フォームの送信時に SMS Manager を通じて SMS 通知を送信します。',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'プラグインの設定エリアから SMS プロバイダーを設定し、Formie の送信通知を接続します。',
    'Open Formie SMS' => 'Formie SMS を開く',

    // Controller messages
    'Failed to send SMS to {recipient}' => '{recipient} への SMS 送信に失敗しました',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'レンダリング後に有効な受信者が見つかりません — SMS は送信されませんでした。統合の「Recipients」テンプレートと送信データを確認してください。',
    'SMS Manager plugin is not installed.' => 'SMS Manager プラグインがインストールされていません。',

    // Settings: General
    'General Settings' => '一般設定',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => '`config/formie-sms.php` ファイルの `pluginName` 設定によってオーバーライドされています。',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'SMS Manager で SMS プロバイダーと送信者 ID を設定し、Formie フォームでこの統合を使用してください。',
    'SMS Manager Integration' => 'SMS Manager 統合',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'このプラグインは {link} と連携し、フォームが送信された際に SMS 通知を送ります。',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'この統合は SMS Manager を使用して SMS メッセージを送信します。{link} の設定でプロバイダーと送信者 ID を設定してください。',

    // Integration: Form settings
    'Any Language' => 'すべての言語',
    'Language Filter' => '言語フィルター',
    'Message' => 'メッセージ',
    'No sender IDs for this provider' => 'このプロバイダーの送信者 ID がありません',
    'Only send SMS when the form is submitted from a specific language site.' => '特定の言語サイトからフォームが送信された場合のみ SMS を送信します。',
    'Provider' => 'プロバイダー',
    'Recipient(s)' => '受信者',
    'Select a provider...' => 'プロバイダーを選択...',
    'Select a sender ID...' => '送信者 ID を選択...',
    'Select the SMS provider to use.' => '使用する SMS プロバイダーを選択してください。',
    'Select the sender ID to use for outgoing messages.' => '送信メッセージに使用する送信者 ID を選択してください。',
    'Sender ID' => '送信者 ID',
    'The SMS message content. Use form field variables to personalize.' => 'SMS メッセージの内容です。フォームフィールドの変数を使用してパーソナライズできます。',
    'Use a comma-separated list for multiple recipients.' => '複数の受信者にはカンマ区切りのリストを使用してください。',
];
