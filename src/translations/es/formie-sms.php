<?php
/**
 * Formie SMS translation file (Spanish)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Envíe notificaciones SMS a través de SMS Manager al enviar formularios.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Configure los proveedores de SMS y conecte las notificaciones de envío de Formie desde el área de configuración del plugin.',
    'Open Formie SMS' => 'Abrir Formie SMS',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Error al enviar SMS a {recipient}',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'No hay destinatarios válidos tras el procesado — SMS no enviado. Compruebe la plantilla «Destinatarios» de la integración y los datos del envío.',
    'SMS Manager plugin is not installed.' => 'El plugin SMS Manager no está instalado.',

    // Settings: General
    'General Settings' => 'Configuración general',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Esta configuración está siendo reemplazada por el parámetro `pluginName` en el archivo `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Configure sus proveedores de SMS e ID de remitente en SMS Manager y, a continuación, utilice esta integración en sus formularios de Formie.',
    'SMS Manager Integration' => 'Integración con SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Este plugin se integra con {link} para enviar notificaciones SMS cuando se envían formularios.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Esta integración utiliza SMS Manager para enviar mensajes SMS. Configure los proveedores y los ID de remitente en los ajustes de {link}.',

    // Integration: Form settings
    'Any Language' => 'Cualquier idioma',
    'Language Filter' => 'Filtro de idioma',
    'Message' => 'Mensaje',
    'No sender IDs for this provider' => 'No hay ID de remitente para este proveedor',
    'Only send SMS when the form is submitted from a specific language site.' => 'Enviar SMS solo cuando el formulario se envíe desde un sitio en un idioma específico.',
    'Provider' => 'Proveedor',
    'Recipient(s)' => 'Destinatario(s)',
    'Select a provider...' => 'Seleccionar un proveedor...',
    'Select a sender ID...' => 'Seleccionar un ID de remitente...',
    'Select the SMS provider to use.' => 'Seleccione el proveedor de SMS que desea utilizar.',
    'Select the sender ID to use for outgoing messages.' => 'Seleccione el ID de remitente para los mensajes salientes.',
    'Sender ID' => 'ID de remitente',
    'The SMS message content. Use form field variables to personalize.' => 'El contenido del mensaje SMS. Utilice variables de campos del formulario para personalizarlo.',
    'Use a comma-separated list for multiple recipients.' => 'Use una lista separada por comas para varios destinatarios.',
];
