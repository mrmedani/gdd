<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$src = file_get_contents("resources/views/livewire/station-charges.blade.php");
$out = \Blade::compileString($src);
file_put_contents("_compiled_local2.php", $out);
echo "if ouverts: ", substr_count($out, "<?php if("), "\n";
echo "endif: ", substr_count($out, "<?php endif; ?>"), "\n";
echo "modal présent: ", (str_contains($out, "fixed inset-0 z-50") ? "OUI" : "NON"), "\n";
echo " Formulaire comment: ", (str_contains($out, "Formulaire") ? "OUI" : "NON"), "\n";
