<?php
/**
 * Formie SMS translation file (Swedish)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Skicka SMS-meddelanden via SMS Manager när formulär skickas in.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Konfigurera SMS-leverantörer och anslut Formie-inlämningsmeddelanden från pluginens inställningsområde.',
    'Open Formie SMS' => 'Öppna Formie SMS',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Det gick inte att skicka SMS till {recipient}.',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Inga giltiga mottagare efter rendering — SMS skickades inte. Kontrollera integrationens mall för «Mottagare» och inlämningsdata.',
    'SMS Manager plugin is not installed.' => 'Plugin-programmet SMS Manager är inte installerat.',

    // Settings: General
    'General Settings' => 'Allmänna inställningar',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Detta åsidosätts av inställningen `pluginName` i filen `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Konfigurera dina SMS-leverantörer och avsändar-ID:n i SMS Manager och använd sedan den här integrationen i dina Formie-formulär.',
    'SMS Manager Integration' => 'SMS Manager-integration',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Det här plugin-programmet integreras med {link} för att skicka SMS-meddelanden när formulär skickas in.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Den här integrationen använder SMS Manager för att skicka SMS-meddelanden. Konfigurera leverantörer och avsändar-ID:n i inställningarna för {link}.',

    // Integration: Form settings
    'Any Language' => 'Valfritt språk',
    'Language Filter' => 'Språkfilter',
    'Message' => 'Meddelande',
    'No sender IDs for this provider' => 'Inga avsändar-ID:n för den här leverantören',
    'Only send SMS when the form is submitted from a specific language site.' => 'Skicka SMS endast när formuläret skickas in från en webbplats med ett specifikt språk.',
    'Provider' => 'Leverantör',
    'Recipient(s)' => 'Mottagare',
    'Select a provider...' => 'Välj en leverantör…',
    'Select a sender ID...' => 'Välj ett avsändar-ID…',
    'Select the SMS provider to use.' => 'Välj den SMS-leverantör som ska användas.',
    'Select the sender ID to use for outgoing messages.' => 'Välj det avsändar-ID som ska användas för utgående meddelanden.',
    'Sender ID' => 'Avsändar-ID',
    'The SMS message content. Use form field variables to personalize.' => 'Innehållet i SMS-meddelandet. Använd formulärfältsvariabler för att anpassa.',
    'Use a comma-separated list for multiple recipients.' => 'Använd en kommaseparerad lista för flera mottagare.',
];
