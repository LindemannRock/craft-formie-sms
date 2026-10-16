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

    // Controller messages
    'SMS Manager has blocked sending for this account or transport. Remaining recipients were not processed. Check SMS Manager before retrying.' => 'SMS Manager har blokeret afsendelse for denne konto eller transport. De resterende modtagere blev ikke behandlet. Kontrollér SMS Manager, før du prøver igen.',
    'Failed to send SMS to {recipient}' => 'Afsendelse af SMS til {recipient} mislykkedes',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Ingen gyldige modtagere efter gengivelse — SMS blev ikke sendt. Kontrollér integrationens skabelon for «Modtagere» og de indsendte data.',
    'SMS Manager plugin is not installed.' => 'Plugin-programmet SMS Manager er ikke installeret.',

    // Settings: General
    'General Settings' => 'Generelle indstillinger',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Konfigurer dine SMS-udbydere og afsender-ID\'er i SMS Manager, og brug derefter denne integration i dine Formie-formularer.',
    'SMS Manager Integration' => 'SMS Manager-integration',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Dette plugin integrerer med {link} for at sende SMS-notifikationer, når formularer indsendes.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Denne integration bruger SMS Manager til at sende SMS-beskeder. Konfigurer udbydere og afsender-ID\'er i indstillingerne for {link}.',

    // Integration: Form settings
    'SMS Manager has paused sending until {time}.' => 'SMS Manager har sat afsendelse på pause indtil {time}.',
    'SMS sending is paused until this hold is released in SMS Manager.' => 'Afsendelse af SMS er sat på pause, indtil denne blokering ophæves i SMS Manager.',
    'Hold status is unavailable here. Check SMS Manager.' => 'Blokeringsstatus er ikke tilgængelig her. Kontrollér SMS Manager.',
    'Affected submissions will not send automatically later. Reload this page to refresh the status.' => 'Berørte indsendelser bliver ikke automatisk sendt senere. Genindlæs denne side for at opdatere status.',
    'Any Language' => 'Alle sprog',
    'Language Filter' => 'Sprogfilter',
    'Message' => 'Besked',
    'Only send SMS when the form is submitted from a specific language site.' => 'Send kun SMS, når formularen indsendes fra et websted med et bestemt sprog.',
    'Recipient(s)' => 'Modtager(e)',
    'Sender ID' => 'Afsender-ID',
    'The SMS message content. Use form field variables to personalize.' => 'Indholdet af SMS-beskeden. Brug formularfeltvariabler til at personalisere.',
    'Use a comma-separated list for multiple recipients.' => 'Brug en kommasepareret liste til flere modtagere.',
];
