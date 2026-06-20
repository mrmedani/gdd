<?php

$files = [
    'resources/lang/ar.json',
    'resources/lang/fr.json',
    'resources/lang/en.json'
];

$newKeys = [
    'settings.desc' => [
        'ar' => 'إدارة إعدادات النظام وتخصيص تجربة المستخدم',
        'fr' => 'Gérer les paramètres du système et personnaliser l\'expérience utilisateur',
        'en' => 'Manage system settings and customize user experience'
    ],
    'settings.categories_link_desc' => [
        'ar' => 'إدارة تصنيفات المصاريف المتكررة',
        'fr' => 'Gérer les catégories de dépenses récurrentes',
        'en' => 'Manage recurring expense categories'
    ],
    'settings.users_link_desc' => [
        'ar' => 'إضافة وتعديل صلاحيات المستخدمين',
        'fr' => 'Ajouter et modifier les permissions des utilisateurs',
        'en' => 'Add and modify user permissions'
    ],
    'settings.audit_link_desc' => [
        'ar' => 'مراقبة التعديلات والعمليات في النظام',
        'fr' => 'Surveiller les modifications et opérations dans le système',
        'en' => 'Monitor modifications and operations in the system'
    ],
    'settings.general' => [
        'ar' => 'إعدادات النظام العامة',
        'fr' => 'Paramètres généraux du système',
        'en' => 'General system settings'
    ],
    'settings.lang_ar' => [
        'ar' => 'العربية (Arabic)',
        'fr' => 'Arabe (Arabic)',
        'en' => 'Arabic (العربية)'
    ],
    'settings.lang_fr' => [
        'ar' => 'الفرنسية (French)',
        'fr' => 'Français (French)',
        'en' => 'French (Français)'
    ],
    'settings.lang_en' => [
        'ar' => 'الإنجليزية (English)',
        'fr' => 'Anglais (English)',
        'en' => 'English (English)'
    ],
    'settings.new_alert' => [
        'ar' => 'جديد',
        'fr' => 'Nouveau',
        'en' => 'New'
    ]
];

foreach ($files as $file) {
    $lang = basename($file, '.json');
    $content = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($newKeys as $key => $translations) {
        if (!isset($content[$key])) {
            $content[$key] = $translations[$lang];
        }
    }
    
    file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Updated $file\n";
}
