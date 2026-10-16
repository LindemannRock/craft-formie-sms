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
    'SMS Manager has blocked sending for this account or transport. Remaining recipients were not processed. Check SMS Manager before retrying.' => 'SMS Manager がこのアカウントまたは通信の送信を停止しました。残りの受信者は処理されていません。再試行する前に SMS Manager を確認してください。',
    'Failed to send SMS to {recipient}' => '{recipient} への SMS 送信に失敗しました',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'レンダリング後に有効な受信者が見つかりません — SMS は送信されませんでした。統合の「受信者」テンプレートと送信データを確認してください。',
    'SMS Manager plugin is not installed.' => 'SMS Manager プラグインがインストールされていません。',

    // Settings: General
    'General Settings' => '一般設定',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'SMS Manager で SMS プロバイダーと送信者 ID を設定し、Formie フォームでこの統合を使用してください。',
    'SMS Manager Integration' => 'SMS Manager 統合',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'このプラグインは {link} と連携し、フォームが送信された際に SMS 通知を送ります。',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'この統合は SMS Manager を使用して SMS メッセージを送信します。{link} の設定でプロバイダーと送信者 ID を設定してください。',

    // Integration: Form settings
    'SMS Manager has paused sending until {time}.' => 'SMS Manager が {time} まで送信を停止しています。',
    'SMS sending is paused until this hold is released in SMS Manager.' => 'SMS Manager でこの送信停止が解除されるまで、SMS は送信されません。',
    'Hold status is unavailable here. Check SMS Manager.' => 'ここでは送信停止の状態を確認できません。SMS Manager を確認してください。',
    'Affected submissions will not send automatically later. Reload this page to refresh the status.' => '対象の送信データは後で自動的に送信されません。状態を更新するには、このページを再読み込みしてください。',
    'Any Language' => 'すべての言語',
    'Language Filter' => '言語フィルター',
    'Message' => 'メッセージ',
    'Only send SMS when the form is submitted from a specific language site.' => '特定の言語サイトからフォームが送信された場合のみ SMS を送信します。',
    'Recipient(s)' => '受信者',
    'Sender ID' => '送信者 ID',
    'The SMS message content. Use form field variables to personalize.' => 'SMS メッセージの内容です。フォームフィールドの変数を使用してパーソナライズできます。',
    'Use a comma-separated list for multiple recipients.' => '複数の受信者にはカンマ区切りのリストを使用してください。',
];
