<?php
/**
 * Formie SMS translation file (Italian)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Invii notifiche SMS tramite SMS Manager all\'invio dei moduli.',
    'Plugin Name' => 'Nome del plugin',
    'The name of the plugin as it appears in the Control Panel menu' => 'Il nome del plugin come appare nel menu del pannello di controllo',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Invio SMS a {recipient} non riuscito.',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Nessun destinatario valido dopo il rendering — SMS non inviato. Verifichi il modello «Destinatari» dell\'integrazione e i dati di invio.',
    'SMS Manager plugin is not installed.' => 'Il plugin SMS Manager non è installato.',

    // Settings: General
    'General Settings' => 'Impostazioni generali',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Questa impostazione viene sovrascritta dal parametro `pluginName` nel file `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Configuri i propri provider SMS e gli ID mittente in SMS Manager, quindi utilizzi questa integrazione nei moduli Formie.',
    'SMS Manager Integration' => 'Integrazione SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Questo plugin si integra con {link} per inviare notifiche SMS quando i moduli vengono inviati.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Questa integrazione utilizza SMS Manager per inviare messaggi SMS. Configuri i provider e gli ID mittente nelle impostazioni di {link}.',

    // Integration: Form settings
    'Any Language' => 'Qualsiasi lingua',
    'Language Filter' => 'Filtro lingua',
    'Message' => 'Messaggio',
    'No sender IDs for this provider' => 'Nessun ID mittente per questo provider',
    'Only send SMS when the form is submitted from a specific language site.' => 'Invii SMS solo quando il modulo viene inviato da un sito in una lingua specifica.',
    'Provider' => 'Provider',
    'Recipient(s)' => 'Destinatario/i',
    'Select a provider...' => 'Seleziona un provider…',
    'Select a sender ID...' => 'Seleziona un ID mittente…',
    'Select the SMS provider to use.' => 'Selezioni il provider SMS da utilizzare.',
    'Select the sender ID to use for outgoing messages.' => 'Selezioni l\'ID mittente da utilizzare per i messaggi in uscita.',
    'Sender ID' => 'ID mittente',
    'The SMS message content. Use form field variables to personalize.' => 'Il contenuto del messaggio SMS. Utilizzi le variabili dei campi del modulo per personalizzarlo.',
    'Use a comma-separated list for multiple recipients.' => 'Utilizzi un elenco separato da virgole per più destinatari.',
];
