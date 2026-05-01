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
    'Plugin Name' => 'Nom du plugin',
    'The name of the plugin as it appears in the Control Panel menu' => 'Le nom du plugin tel qu\'il apparaît dans le menu du panneau de contrôle',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'Échec de l\'envoi du SMS à {recipient}.',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'Aucun destinataire valide après le rendu — SMS non envoyé. Vérifiez le modèle « Destinataires » de l\'intégration et les données de soumission.',
    'SMS Manager plugin is not installed.' => 'Le plugin SMS Manager n\'est pas installé.',

    // Settings: General
    'General Settings' => 'Paramètres généraux',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'Ce paramètre est remplacé par le paramètre `pluginName` dans le fichier `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'Configurez vos fournisseurs SMS et vos identifiants d\'expéditeur dans SMS Manager, puis utilisez cette intégration dans vos formulaires Formie.',
    'SMS Manager Integration' => 'Intégration SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'Ce plugin s\'intègre à {link} pour envoyer des notifications SMS lors de la soumission de formulaires.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'Cette intégration utilise SMS Manager pour envoyer des messages SMS. Configurez les fournisseurs et les identifiants d\'expéditeur dans les paramètres de {link}.',

    // Integration: Form settings
    'Any Language' => 'Toutes les langues',
    'Language Filter' => 'Filtre de langue',
    'Message' => 'Message',
    'No sender IDs for this provider' => 'Aucun identifiant d\'expéditeur pour ce fournisseur',
    'Only send SMS when the form is submitted from a specific language site.' => 'Envoyer des SMS uniquement lorsque le formulaire est soumis depuis un site dans une langue spécifique.',
    'Provider' => 'Fournisseur',
    'Recipient(s)' => 'Destinataire(s)',
    'Select a provider...' => 'Sélectionner un fournisseur…',
    'Select a sender ID...' => 'Sélectionner un identifiant d\'expéditeur…',
    'Select the SMS provider to use.' => 'Sélectionnez le fournisseur SMS à utiliser.',
    'Select the sender ID to use for outgoing messages.' => 'Sélectionnez l\'identifiant d\'expéditeur à utiliser pour les messages sortants.',
    'Sender ID' => 'Identifiant d\'expéditeur',
    'The SMS message content. Use form field variables to personalize.' => 'Le contenu du message SMS. Utilisez des variables de champ de formulaire pour personnaliser.',
    'Use a comma-separated list for multiple recipients.' => 'Utilisez une liste séparée par des virgules pour plusieurs destinataires.',
];
