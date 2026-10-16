<?php
/**
 * Formie SMS translation file (French)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'Envoyez des notifications SMS via SMS Manager lors de la soumission de formulaires.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'Configurez les fournisseurs SMS et connectez les notifications de soumission Formie depuis la zone des paramètres du plugin.',
    'Open Formie SMS' => 'Ouvrir Formie SMS',

    // Controller messages
    'SMS Manager has blocked sending for this account or transport. Remaining recipients were not processed. Check SMS Manager before retrying.' => 'SMS Manager a bloqué l\'envoi pour ce compte ou ce transport. Les destinataires restants n\'ont pas été traités. Vérifiez SMS Manager avant de réessayer.',
    'Failed to send SMS to {recipient}' => 'Échec de l\'envoi du SMS à {recipient}',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Aucun destinataire valide après le rendu — SMS non envoyé. Vérifiez le modèle « Destinataires » de l\'intégration et les données de soumission.',
    'SMS Manager plugin is not installed.' => 'Le plugin SMS Manager n\'est pas installé.',

    // Settings: General
    'General Settings' => 'Paramètres généraux',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Configurez vos fournisseurs SMS et vos identifiants d\'expéditeur dans SMS Manager, puis utilisez cette intégration dans vos formulaires Formie.',
    'SMS Manager Integration' => 'Intégration SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Ce plugin s\'intègre à {link} pour envoyer des notifications SMS lors de la soumission de formulaires.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Cette intégration utilise SMS Manager pour envoyer des messages SMS. Configurez les fournisseurs et les identifiants d\'expéditeur dans les paramètres de {link}.',

    // Integration: Form settings
    'SMS Manager has paused sending until {time}.' => 'SMS Manager a suspendu l\'envoi jusqu\'à {time}.',
    'SMS sending is paused until this hold is released in SMS Manager.' => 'L\'envoi de SMS est suspendu jusqu\'à la levée de ce blocage dans SMS Manager.',
    'Hold status is unavailable here. Check SMS Manager.' => 'Le statut du blocage n\'est pas disponible ici. Vérifiez SMS Manager.',
    'Affected submissions will not send automatically later. Reload this page to refresh the status.' => 'Les soumissions concernées ne seront pas envoyées automatiquement plus tard. Rechargez cette page pour actualiser le statut.',
    'Any Language' => 'Toutes les langues',
    'Language Filter' => 'Filtre de langue',
    'Message' => 'Message',
    'Only send SMS when the form is submitted from a specific language site.' => 'Envoyer des SMS uniquement lorsque le formulaire est soumis depuis un site dans une langue spécifique.',
    'Recipient(s)' => 'Destinataire(s)',
    'Sender ID' => 'Identifiant d\'expéditeur',
    'The SMS message content. Use form field variables to personalize.' => 'Le contenu du message SMS. Utilisez des variables de champ de formulaire pour personnaliser.',
    'Use a comma-separated list for multiple recipients.' => 'Utilisez une liste séparée par des virgules pour plusieurs destinataires.',
];
