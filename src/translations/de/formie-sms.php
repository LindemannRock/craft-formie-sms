<?php
/**
 * Formie SMS translation file (German)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Senden Sie SMS-Benachrichtigungen über SMS Manager bei Formularübermittlungen.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Konfigurieren Sie SMS-Anbieter und verknüpfen Sie Formie-Übermittlungsbenachrichtigungen im Einstellungsbereich des Plugins.',
    'Open Formie SMS' => 'Formie SMS öffnen',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'SMS-Versand an {recipient} fehlgeschlagen',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Nach dem Rendern keine gültigen Empfänger gefunden — SMS wurde nicht gesendet. Überprüfen Sie die „Empfänger"-Vorlage der Integration und die Übermittlungsdaten.',
    'SMS Manager plugin is not installed.' => 'Das SMS Manager-Plugin ist nicht installiert.',

    // Settings: General
    'General Settings' => 'Allgemeine Einstellungen',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Dies wird durch die Einstellung `pluginName` in der Datei `config/formie-sms.php` überschrieben.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Konfigurieren Sie Ihre SMS-Anbieter und Absender-IDs in SMS Manager und verwenden Sie dann diese Integration in Ihren Formie-Formularen.',
    'SMS Manager Integration' => 'SMS Manager-Integration',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Dieses Plugin ist mit {link} integriert, um SMS-Benachrichtigungen beim Einreichen von Formularen zu senden.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Diese Integration verwendet SMS Manager zum Versand von SMS-Nachrichten. Konfigurieren Sie Anbieter und Absender-IDs in den {link}-Einstellungen.',

    // Integration: Form settings
    'Any Language' => 'Beliebige Sprache',
    'Language Filter' => 'Sprachfilter',
    'Message' => 'Nachricht',
    'No sender IDs for this provider' => 'Keine Absender-IDs für diesen Anbieter',
    'Only send SMS when the form is submitted from a specific language site.' => 'SMS nur senden, wenn das Formular von einer bestimmten Sprachwebsite eingereicht wird.',
    'Provider' => 'Anbieter',
    'Recipient(s)' => 'Empfänger',
    'Select a provider...' => 'Anbieter auswählen ...',
    'Select a sender ID...' => 'Absender-ID auswählen ...',
    'Select the SMS provider to use.' => 'Wählen Sie den zu verwendenden SMS-Anbieter aus.',
    'Select the sender ID to use for outgoing messages.' => 'Wählen Sie die Absender-ID für ausgehende Nachrichten aus.',
    'Sender ID' => 'Absender-ID',
    'The SMS message content. Use form field variables to personalize.' => 'Der Inhalt der SMS-Nachricht. Verwenden Sie Formularfeldvariablen zur Personalisierung.',
    'Use a comma-separated list for multiple recipients.' => 'Verwenden Sie eine kommagetrennte Liste für mehrere Empfänger.',
];
