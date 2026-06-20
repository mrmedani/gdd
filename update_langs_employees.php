<?php

$files = [
    'resources/lang/ar.json',
    'resources/lang/fr.json',
    'resources/lang/en.json'
];

$newKeys = [
    'employees.list_desc' => [
        'ar' => 'عرض وإدارة قائمة الموظفين وسجلات الرواتب والسلف',
        'fr' => 'Afficher et gérer la liste des employés, des salaires et des avances',
        'en' => 'View and manage the list of employees, salaries, and advances'
    ],
    'employees.title_desc' => [
        'ar' => 'إدارة الملف الشخصي للموظف وسجلات الرواتب والسلف',
        'fr' => 'Gérer le profil de l\'employé, les salaires et les avances',
        'en' => 'Manage employee profile, salaries, and advances'
    ],
    'employees.adv_status_deducted' => [
        'ar' => 'مخصومة',
        'fr' => 'Déduite',
        'en' => 'Deducted'
    ],
    'employees.adv_status_pending' => [
        'ar' => 'معلقة',
        'fr' => 'En attente',
        'en' => 'Pending'
    ],
    'employees.base_salary_label' => [
        'ar' => 'الراتب الأساسي:',
        'fr' => 'Salaire de base:',
        'en' => 'Base salary:'
    ],
    'employees.pending_advances_label' => [
        'ar' => 'السلف المعلقة:',
        'fr' => 'Avances en attente:',
        'en' => 'Pending advances:'
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
