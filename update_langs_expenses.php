<?php

$files = [
    'resources/lang/ar.json',
    'resources/lang/fr.json',
    'resources/lang/en.json'
];

$newKeys = [
    'expenses.list_desc' => [
        'ar' => 'إدارة جميع مصاريف الشركة وعمليات الصرف',
        'fr' => 'Gérer toutes les dépenses de l\'entreprise et les opérations de paiement',
        'en' => 'Manage all company expenses and payment operations'
    ],
    'expenses.receipt_attached' => [
        'ar' => 'مرفق',
        'fr' => 'Pièce jointe',
        'en' => 'Attached'
    ],
    'expenses.no_expenses_desc' => [
        'ar' => 'لم يتم العثور على أي مصاريف مطابقة للبحث',
        'fr' => 'Aucune dépense correspondant à la recherche n\'a été trouvée',
        'en' => 'No expenses matching the search were found'
    ],
    'expenses.form_desc' => [
        'ar' => 'قم بملء البيانات المطلوبة لحفظ المصروف في السجلات',
        'fr' => 'Remplissez les informations requises pour enregistrer la dépense',
        'en' => 'Fill in the required information to save the expense'
    ],
    'expenses.desc_placeholder' => [
        'ar' => 'أدخل تفاصيل المصروف بدقة...',
        'fr' => 'Entrez les détails de la dépense...',
        'en' => 'Enter the expense details...'
    ],
    'expenses.notes_placeholder' => [
        'ar' => 'ملاحظات إضافية (اختياري)...',
        'fr' => 'Notes supplémentaires (facultatif)...',
        'en' => 'Additional notes (optional)...'
    ],
    'expenses.remove_receipt' => [
        'ar' => 'إلغاء الصورة',
        'fr' => 'Supprimer l\'image',
        'en' => 'Remove image'
    ],
    'expenses.drag_drop_receipt' => [
        'ar' => 'اسحب الفاتورة أو اضغط للرفع',
        'fr' => 'Glissez la facture ou cliquez pour télécharger',
        'en' => 'Drag the receipt or click to upload'
    ],
    'expenses.receipt_help' => [
        'ar' => 'صور أو PDF (الحد الأقصى 5MB)',
        'fr' => 'Images ou PDF (Max 5MB)',
        'en' => 'Images or PDF (Max 5MB)'
    ],
    'expenses.current_receipt' => [
        'ar' => 'الفاتورة الحالية',
        'fr' => 'Facture actuelle',
        'en' => 'Current receipt'
    ],
    'expenses.view_file' => [
        'ar' => 'عرض الملف',
        'fr' => 'Voir le fichier',
        'en' => 'View file'
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
