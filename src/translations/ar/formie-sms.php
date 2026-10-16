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
    'SMS Manager has blocked sending for this account or transport. Remaining recipients were not processed. Check SMS Manager before retrying.' => 'حظر SMS Manager الإرسال لهذا الحساب أو مسار النقل. لم تتم معالجة المستلمين المتبقين. يرجى التحقق من SMS Manager قبل إعادة المحاولة.',
    'Failed to send SMS to {recipient}' => 'فشل إرسال SMS إلى {recipient}',
    'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.' => 'لا يوجد مستلمون صالحون بعد المعالجة — لم يُرسَل SMS. تحقق من قالب «المستلمون» في التكامل وبيانات الإرسال.',
    'SMS Manager plugin is not installed.' => 'إضافة SMS Manager غير مثبتة.',

    // Settings: General
    'General Settings' => 'الإعدادات العامة',

    // Settings: Integration Information
    'Configure your SMS providers and sender IDs in SMS Manager, then use this integration in your Formie forms.' => 'قم بتكوين موفري SMS ومعرفات المرسل في SMS Manager، ثم استخدم هذا التكامل في نماذج Formie الخاصة بك.',
    'SMS Manager Integration' => 'تكامل SMS Manager',
    'This plugin integrates with {link} to send SMS notifications when forms are submitted.' => 'تتكامل هذه الإضافة مع {link} لإرسال إشعارات SMS عند تقديم النماذج.',

    // Integration: Plugin settings
    'SMS Manager' => 'SMS Manager',
    'This integration uses SMS Manager to send SMS messages. Configure providers and sender IDs in the {link} settings.' => 'يستخدم هذا التكامل SMS Manager لإرسال رسائل SMS. قم بتكوين الموفرين ومعرفات المرسل في إعدادات {link}.',

    // Integration: Form settings
    'SMS Manager has paused sending until {time}.' => 'أوقف SMS Manager الإرسال مؤقتًا حتى {time}.',
    'SMS sending is paused until this hold is released in SMS Manager.' => 'تم إيقاف إرسال SMS مؤقتًا حتى رفع هذا الحظر في SMS Manager.',
    'Hold status is unavailable here. Check SMS Manager.' => 'حالة الحظر غير متاحة هنا. يرجى التحقق من SMS Manager.',
    'Affected submissions will not send automatically later. Reload this page to refresh the status.' => 'لن يتم إرسال الطلبات المتأثرة تلقائيًا لاحقًا. يرجى إعادة تحميل هذه الصفحة لتحديث الحالة.',
    'Any Language' => 'أي لغة',
    'Language Filter' => 'تصفية اللغة',
    'Message' => 'الرسالة',
    'Only send SMS when the form is submitted from a specific language site.' => 'أرسل SMS فقط عند تقديم النموذج من موقع بلغة محددة.',
    'Recipient(s)' => 'المستلم(ون)',
    'Sender ID' => 'معرف المرسل',
    'The SMS message content. Use form field variables to personalize.' => 'محتوى رسالة SMS. استخدم متغيرات حقول النموذج للتخصيص.',
    'Use a comma-separated list for multiple recipients.' => 'استخدم قائمة مفصولة بفواصل لمتعدد المستلمين.',
];
