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
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Configuri i provider SMS e colleghi le notifiche di invio Formie dall\'area delle impostazioni del plugin.',
    'Open Formie SMS' => 'Apri Formie SMS',

    // Controller messages
    'SMS Manager has blocked sending for this account or transport. Remaining recipients were not processed. Check SMS Manager before retrying.' => 'SMS Manager ha bloccato l\'invio per questo account o trasporto. I destinatari rimanenti non sono stati elaborati. Verificare SMS Manager prima di riprovare.',
    'Failed to send SMS to {recipient}' => 'Invio SMS a {recipient} non riuscito',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Nessun destinatario valido dopo il rendering — SMS non inviato. Verifichi il modello «Destinatari» dell\'integrazione e i dati di invio.',
    'SMS Manager plugin is not installed.' => 'Il plugin SMS Manager non è installato.',

    // Settings: General
    'General Settings' => 'Impostazioni generali',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Configuri i propri provider SMS e gli ID mittente in SMS Manager, quindi utilizzi questa integrazione nei moduli Formie.',
    'SMS Manager Integration' => 'Integrazione SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Questo plugin si integra con {link} per inviare notifiche SMS quando i moduli vengono inviati.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Questa integrazione utilizza SMS Manager per inviare messaggi SMS. Configuri i provider e gli ID mittente nelle impostazioni di {link}.',

    // Integration: Form settings
    'SMS Manager has paused sending until {time}.' => 'SMS Manager ha sospeso l\'invio fino a {time}.',
    'SMS sending is paused until this hold is released in SMS Manager.' => 'L\'invio di SMS è sospeso fino alla rimozione di questo blocco in SMS Manager.',
    'Hold status is unavailable here. Check SMS Manager.' => 'Lo stato del blocco non è disponibile qui. Verificare SMS Manager.',
    'Affected submissions will not send automatically later. Reload this page to refresh the status.' => 'Gli invii interessati non verranno inviati automaticamente in seguito. Ricaricare questa pagina per aggiornare lo stato.',
    'Any Language' => 'Qualsiasi lingua',
    'Language Filter' => 'Filtro lingua',
    'Message' => 'Messaggio',
    'Only send SMS when the form is submitted from a specific language site.' => 'Invii SMS solo quando il modulo viene inviato da un sito in una lingua specifica.',
    'Recipient(s)' => 'Destinatario/i',
    'Sender ID' => 'ID mittente',
    'The SMS message content. Use form field variables to personalize.' => 'Il contenuto del messaggio SMS. Utilizzi le variabili dei campi del modulo per personalizzarlo.',
    'Use a comma-separated list for multiple recipients.' => 'Utilizzi un elenco separato da virgole per più destinatari.',
];
