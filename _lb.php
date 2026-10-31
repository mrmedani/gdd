<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1) label court du bouton : « 21 oct. → 20 nov. 2026 »
$range = getPeriodRange('2026-11');
$lbl = $range['start']->locale(app()->getLocale())->translatedFormat('j M') . ' → ' . $range['end']->translatedFormat('j M Y');
echo "button label: $lbl\n";

// 2) noms des mois (labels statiques du grid, locale courante)
echo "months: ";
foreach ([1,2,3,10,12] as $m) {
    echo \Carbon\Carbon::createFromDate(2026, $m, 1)->locale(app()->getLocale())->translatedFormat('F'), " ";
}
echo "\n";
