<?php
/**
 * Formie SMS translation file (Portuguese)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Envie notificações SMS via SMS Manager ao submeter formulários.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Configure os fornecedores de SMS e ligue as notificações de submissão Formie a partir da área de definições do plugin.',
    'Open Formie SMS' => 'Abrir Formie SMS',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Falha ao enviar SMS para {recipient}.',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Nenhum destinatário válido após o processamento — SMS não enviado. Verifique o modelo «Destinatários» da integração e os dados de submissão.',
    'SMS Manager plugin is not installed.' => 'O plugin SMS Manager não está instalado.',

    // Settings: General
    'General Settings' => 'Definições gerais',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Esta definição está a ser substituída pelo parâmetro `pluginName` no ficheiro `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Configure os seus fornecedores de SMS e IDs de remetente no SMS Manager e, em seguida, utilize esta integração nos seus formulários Formie.',
    'SMS Manager Integration' => 'Integração com SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Este plugin integra-se com {link} para enviar notificações SMS quando os formulários são submetidos.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Esta integração utiliza o SMS Manager para enviar mensagens SMS. Configure os fornecedores e os IDs de remetente nas definições de {link}.',

    // Integration: Form settings
    'Any Language' => 'Qualquer idioma',
    'Language Filter' => 'Filtro de idioma',
    'Message' => 'Mensagem',
    'No sender IDs for this provider' => 'Sem IDs de remetente para este fornecedor',
    'Only send SMS when the form is submitted from a specific language site.' => 'Enviar SMS apenas quando o formulário for submetido a partir de um site num idioma específico.',
    'Provider' => 'Fornecedor',
    'Recipient(s)' => 'Destinatário(s)',
    'Select a provider...' => 'Selecionar um fornecedor…',
    'Select a sender ID...' => 'Selecionar um ID de remetente…',
    'Select the SMS provider to use.' => 'Selecione o fornecedor de SMS a utilizar.',
    'Select the sender ID to use for outgoing messages.' => 'Selecione o ID de remetente a utilizar para as mensagens enviadas.',
    'Sender ID' => 'ID de remetente',
    'The SMS message content. Use form field variables to personalize.' => 'O conteúdo da mensagem SMS. Utilize variáveis de campos do formulário para personalizar.',
    'Use a comma-separated list for multiple recipients.' => 'Utilize uma lista separada por vírgulas para múltiplos destinatários.',
];
