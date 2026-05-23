<?php
/**
 * Formie SMS translation file (Dutch)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Verstuur SMS-meldingen via SMS Manager bij het indienen van formulieren.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Configureer SMS-providers en koppel Formie-inzendingsmeldingen vanuit het instellingengebied van de plugin.',
    'Open Formie SMS' => 'Formie SMS openen',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Verzenden van SMS naar {recipient} mislukt.',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Geen geldige ontvangers na het renderen — SMS niet verzonden. Controleer de sjabloon "Ontvangers" van de integratie en de ingediende gegevens.',
    'SMS Manager plugin is not installed.' => 'De SMS Manager-plugin is niet geïnstalleerd.',

    // Settings: General
    'General Settings' => 'Algemene instellingen',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Dit wordt overschreven door de instelling `pluginName` in het bestand `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Configureer uw SMS-providers en afzender-ID\'s in SMS Manager en gebruik deze integratie vervolgens in uw Formie-formulieren.',
    'SMS Manager Integration' => 'SMS Manager-integratie',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Deze plugin integreert met {link} om SMS-meldingen te verzenden wanneer formulieren worden ingediend.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Deze integratie gebruikt SMS Manager om SMS-berichten te verzenden. Configureer providers en afzender-ID\'s in de {link}-instellingen.',

    // Integration: Form settings
    'Any Language' => 'Elke taal',
    'Language Filter' => 'Taalfilter',
    'Message' => 'Bericht',
    'No sender IDs for this provider' => 'Geen afzender-ID\'s voor deze provider',
    'Only send SMS when the form is submitted from a specific language site.' => 'Stuur alleen SMS wanneer het formulier wordt ingediend vanuit een specifieke taalsite.',
    'Provider' => 'Provider',
    'Recipient(s)' => 'Ontvanger(s)',
    'Select a provider...' => 'Selecteer een provider…',
    'Select a sender ID...' => 'Selecteer een afzender-ID…',
    'Select the SMS provider to use.' => 'Selecteer de te gebruiken SMS-provider.',
    'Select the sender ID to use for outgoing messages.' => 'Selecteer de afzender-ID voor uitgaande berichten.',
    'Sender ID' => 'Afzender-ID',
    'The SMS message content. Use form field variables to personalize.' => 'De inhoud van het SMS-bericht. Gebruik formulierveldvariabelen om te personaliseren.',
    'Use a comma-separated list for multiple recipients.' => 'Gebruik een door komma\'s gescheiden lijst voor meerdere ontvangers.',
];
