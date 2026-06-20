<?php

$files = [
    'resources/lang/ar.json',
    'resources/lang/fr.json',
    'resources/lang/en.json'
];

$newKeys = [
    'reports.index_desc' => [
        'ar' => 'تصدير تقارير المصاريف الشهرية والسنوية بصيغة PDF أو Excel',
        'fr' => 'Exporter les rapports de dépenses mensuels et annuels au format PDF ou Excel',
        'en' => 'Export monthly and annual expense reports in PDF or Excel format'
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
