<?php
/**
 * Formie SMS translation file (Norwegian)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Send SMS-varsler via SMS Manager ved innsending av skjemaer.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Konfigurer SMS-leverandører og koble Formie-innsendingsvarsler fra pluginens innstillingsområde.',
    'Open Formie SMS' => 'Åpne Formie SMS',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Sending av SMS til {recipient} mislyktes',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Ingen gyldige mottakere etter rendering — SMS ble ikke sendt. Kontroller integrasjonens mal for «Mottakere» og de innsendte dataene.',
    'SMS Manager plugin is not installed.' => 'Plugin-programmet SMS Manager er ikke installert.',

    // Settings: General
    'General Settings' => 'Generelle innstillinger',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Dette overstyres av innstillingen `pluginName` i filen `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Konfigurer SMS-leverandørene og avsender-ID-ene dine i SMS Manager, og bruk deretter denne integrasjonen i Formie-skjemaene dine.',
    'SMS Manager Integration' => 'SMS Manager-integrasjon',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Dette pluginet integrerer med {link} for å sende SMS-varsler når skjemaer sendes inn.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Denne integrasjonen bruker SMS Manager til å sende SMS-meldinger. Konfigurer leverandører og avsender-ID-er i innstillingene for {link}.',

    // Integration: Form settings
    'Any Language' => 'Alle språk',
    'Language Filter' => 'Språkfilter',
    'Message' => 'Melding',
    'No sender IDs for this provider' => 'Ingen avsender-ID-er for denne leverandøren',
    'Only send SMS when the form is submitted from a specific language site.' => 'Send kun SMS når skjemaet sendes inn fra et nettsted med et bestemt språk.',
    'Provider' => 'Leverandør',
    'Recipient(s)' => 'Mottaker(e)',
    'Select a provider...' => 'Velg en leverandør...',
    'Select a sender ID...' => 'Velg en avsender-ID...',
    'Select the SMS provider to use.' => 'Velg den SMS-leverandøren som skal brukes.',
    'Select the sender ID to use for outgoing messages.' => 'Velg avsender-ID-en som skal brukes for utgående meldinger.',
    'Sender ID' => 'Avsender-ID',
    'The SMS message content. Use form field variables to personalize.' => 'Innholdet i SMS-meldingen. Bruk skjemafeltvariabler for å tilpasse.',
    'Use a comma-separated list for multiple recipients.' => 'Bruk en kommaseparert liste for flere mottakere.',
];
