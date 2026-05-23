<?php
/**
 * Formie SMS translation file (Arabic)
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

return [
    // Plugin meta
    'Formie SMS' => 'Formie SMS',
    'Send SMS notifications via SMS Manager on form submission.' => 'إرسال إشعارات SMS عبر SMS Manager عند تقديم النماذج.',
    'Configure SMS providers and connect Formie submission notifications from the plugin settings area.' => 'قم بتكوين موفري SMS وربط إشعارات إرسال Formie من منطقة إعدادات الإضافة.',
    'Open Formie SMS' => 'فتح Formie SMS',

    // Controller messages
    'Failed to send SMS to {recipient}' => 'فشل إرسال SMS إلى {recipient}.',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'لا يوجد مستلمون صالحون بعد المعالجة — لم يُرسَل SMS. تحقق من قالب «المستلمون» في التكامل وبيانات الإرسال.',
    'SMS Manager plugin is not installed.' => 'إضافة SMS Manager غير مثبتة.',

    // Settings: General
    'General Settings' => 'الإعدادات العامة',
    'This is being overridden by the `pluginName` setting in the `config/formie-sms.php` file.' => 'يتم تجاوز هذا الإعداد بواسطة الإعداد `pluginName` في ملف `config/formie-sms.php`.',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'قم بتكوين موفري SMS ومعرفات المرسل في SMS Manager، ثم استخدم هذا التكامل في نماذج Formie الخاصة بك.',
    'SMS Manager Integration' => 'تكامل SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'تتكامل هذه الإضافة مع {link} لإرسال إشعارات SMS عند تقديم النماذج.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'يستخدم هذا التكامل SMS Manager لإرسال رسائل SMS. قم بتكوين الموفرين ومعرفات المرسل في إعدادات {link}.',

    // Integration: Form settings
    'Any Language' => 'أي لغة',
    'Language Filter' => 'تصفية اللغة',
    'Message' => 'الرسالة',
    'No sender IDs for this provider' => 'لا توجد معرفات مرسل لهذا الموفر',
    'Only send SMS when the form is submitted from a specific language site.' => 'أرسل SMS فقط عند تقديم النموذج من موقع بلغة محددة.',
    'Provider' => 'الموفر',
    'Recipient(s)' => 'المستلم(ون)',
    'Select a provider...' => 'اختر موفرًا…',
    'Select a sender ID...' => 'اختر معرف مرسل…',
    'Select the SMS provider to use.' => 'اختر موفر SMS المراد استخدامه.',
    'Select the sender ID to use for outgoing messages.' => 'اختر معرف المرسل لاستخدامه في الرسائل الصادرة.',
    'Sender ID' => 'معرف المرسل',
    'The SMS message content. Use form field variables to personalize.' => 'محتوى رسالة SMS. استخدم متغيرات حقول النموذج للتخصيص.',
    'Use a comma-separated list for multiple recipients.' => 'استخدم قائمة مفصولة بفواصل لمتعدد المستلمين.',
];
