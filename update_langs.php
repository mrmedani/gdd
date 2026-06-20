<?php

$files = [
    'resources/lang/ar.json',
    'resources/lang/fr.json',
    'resources/lang/en.json'
];

$newKeys = [
    'dashboard.subtitle' => [
        'ar' => 'نظرة عامة على مصاريف الشركة',
        'fr' => 'Aperçu des dépenses de l\'entreprise',
        'en' => 'Company expenses overview'
    ],
    'dashboard.day' => [
        'ar' => 'يوم',
        'fr' => 'jour',
        'en' => 'day'
    ],
    'dashboard.healthy_average' => [
        'ar' => 'متوسط سليم',
        'fr' => 'Moyenne saine',
        'en' => 'Healthy average'
    ],
    'audit.subtitle' => [
        'ar' => 'مراقبة جميع العمليات والتعديلات التي تمت على النظام',
        'fr' => 'Surveiller toutes les opérations et modifications effectuées sur le système',
        'en' => 'Monitor all operations and modifications made on the system'
    ],
    'audit.no_records' => [
        'ar' => 'لا توجد سجلات',
        'fr' => 'Aucun enregistrement',
        'en' => 'No records'
    ],
    'settings.categories_desc' => [
        'ar' => 'إدارة وإضافة التصنيفات الخاصة بالمصاريف',
        'fr' => 'Gérer et ajouter des catégories de dépenses',
        'en' => 'Manage and add expense categories'
    ],
    'settings.add_new_category' => [
        'ar' => 'إضافة تصنيف جديد',
        'fr' => 'Ajouter une nouvelle catégorie',
        'en' => 'Add new category'
    ],
    'categories.name_ar' => [
        'ar' => 'الاسم (بالعربية)',
        'fr' => 'Nom (en Arabe)',
        'en' => 'Name (Arabic)'
    ],
    'categories.name_fr' => [
        'ar' => 'الاسم (بالفرنسية)',
        'fr' => 'Nom (en Français)',
        'en' => 'Name (French)'
    ],
    'categories.name_en' => [
        'ar' => 'الاسم (بالإنجليزية)',
        'fr' => 'Nom (en Anglais)',
        'en' => 'Name (English)'
    ],
    'categories.key' => [
        'ar' => 'المفتاح (Key)',
        'fr' => 'Clé (Key)',
        'en' => 'Key'
    ],
    'categories.key_auto' => [
        'ar' => 'يتم توليده تلقائياً إذا تُرك فارغاً',
        'fr' => 'Généré automatiquement si laissé vide',
        'en' => 'Generated automatically if left empty'
    ],
    'categories.is_active' => [
        'ar' => 'نشط (متاح للاستخدام)',
        'fr' => 'Actif (disponible pour utilisation)',
        'en' => 'Active (available for use)'
    ],
    'categories.col_name_ar' => [
        'ar' => 'الاسم بالعربية',
        'fr' => 'Nom en Arabe',
        'en' => 'Name in Arabic'
    ],
    'categories.col_name_fr' => [
        'ar' => 'الاسم بالفرنسية',
        'fr' => 'Nom en Français',
        'en' => 'Name in French'
    ],
    'categories.col_key' => [
        'ar' => 'المفتاح',
        'fr' => 'Clé',
        'en' => 'Key'
    ],
    'categories.col_linked_expenses' => [
        'ar' => 'المصاريف المرتبطة',
        'fr' => 'Dépenses liées',
        'en' => 'Linked expenses'
    ],
    'categories.col_status' => [
        'ar' => 'الحالة',
        'fr' => 'Statut',
        'en' => 'Status'
    ],
    'categories.status_active' => [
        'ar' => 'نشط',
        'fr' => 'Actif',
        'en' => 'Active'
    ],
    'categories.status_inactive' => [
        'ar' => 'غير نشط',
        'fr' => 'Inactif',
        'en' => 'Inactive'
    ],
    'categories.confirm_delete' => [
        'ar' => 'هل أنت متأكد من الحذف؟',
        'fr' => 'Êtes-vous sûr de vouloir supprimer ?',
        'en' => 'Are you sure you want to delete?'
    ],
    'categories.cannot_delete_linked' => [
        'ar' => 'لا يمكن حذفه لوجود مصاريف مرتبطة',
        'fr' => 'Impossible de supprimer car des dépenses sont liées',
        'en' => 'Cannot delete because expenses are linked'
    ],
    'categories.no_categories' => [
        'ar' => 'لا توجد تصنيفات مسجلة',
        'fr' => 'Aucune catégorie enregistrée',
        'en' => 'No categories registered'
    ],
    'settings.categories' => [
        'ar' => 'تصنيفات المصاريف',
        'fr' => 'Catégories de dépenses',
        'en' => 'Expense categories'
    ],
    'settings.add_category' => [
        'ar' => 'إضافة تصنيف',
        'fr' => 'Ajouter une catégorie',
        'en' => 'Add category'
    ],
    'settings.edit_category' => [
        'ar' => 'تعديل تصنيف',
        'fr' => 'Modifier une catégorie',
        'en' => 'Edit category'
    ],
    'settings.users_desc' => [
        'ar' => 'إضافة وتعديل المستخدمين وصلاحياتهم في النظام',
        'fr' => 'Ajouter et modifier les utilisateurs et leurs permissions dans le système',
        'en' => 'Add and modify users and their permissions in the system'
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
