<?php
/**
 * Formie SMS translation file (Danish)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Send SMS-notifikationer via SMS Manager ved indsendelse af formularer.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Konfigurer SMS-udbydere og tilslut Formie-indsendelsesnotifikationer fra pluginens indstillingsområde.',
    'Open Formie SMS' => 'Åbn Formie SMS',
    'Plugin Name' => 'Plugin-navn',
    'The name of the plugin as it appears in the Control Panel menu' => 'Navnet på pluginnet som det vises i kontrolpanelets menu',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Afsendelse af SMS til {recipient} mislykkedes.',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Ingen gyldige modtagere efter gengivelse — SMS blev ikke sendt. Kontrollér integrationens skabelon for «Modtagere» og de indsendte data.',
    'SMS Manager plugin is not installed.' => 'Plugin-programmet SMS Manager er ikke installeret.',

    // Settings: General
    'General Settings' => 'Generelle indstillinger',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Dette tilsidesættes af indstillingen `pluginName` i filen `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Konfigurér dine SMS-udbydere og afsender-ID\'er i SMS Manager, og brug derefter denne integration i dine Formie-formularer.',
    'SMS Manager Integration' => 'SMS Manager-integration',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Dette plugin integrerer med {link} for at sende SMS-notifikationer, når formularer indsendes.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Denne integration bruger SMS Manager til at sende SMS-beskeder. Konfigurér udbydere og afsender-ID\'er i indstillingerne for {link}.',

    // Integration: Form settings
    'Any Language' => 'Alle sprog',
    'Language Filter' => 'Sprogfilter',
    'Message' => 'Besked',
    'No sender IDs for this provider' => 'Ingen afsender-ID\'er for denne udbyder',
    'Only send SMS when the form is submitted from a specific language site.' => 'Send kun SMS, når formularen indsendes fra et websted med et bestemt sprog.',
    'Provider' => 'Udbyder',
    'Recipient(s)' => 'Modtager(e)',
    'Select a provider...' => 'Vælg en udbyder…',
    'Select a sender ID...' => 'Vælg et afsender-ID…',
    'Select the SMS provider to use.' => 'Vælg den SMS-udbyder, der skal bruges.',
    'Select the sender ID to use for outgoing messages.' => 'Vælg det afsender-ID, der skal bruges til udgående beskeder.',
    'Sender ID' => 'Afsender-ID',
    'The SMS message content. Use form field variables to personalize.' => 'Indholdet af SMS-beskeden. Brug formularfeltvariabler til at personalisere.',
    'Use a comma-separated list for multiple recipients.' => 'Brug en kommasepareret liste til flere modtagere.',
];
