<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$src = file_get_contents("resources/views/livewire/station-charges.blade.php");
$out = \Blade::compileString($src);
file_put_contents("_compiled_local.php", $out);
// comptes ENDBLOCK / BLOCK comme Livewire les émet
echo "if BLOCK: ", substr_count($out, "[if BLOCK]"), "\n";
echo "ENDBLOCK: ", substr_count($out, "ENDBLOCK]><![endif]"), "\n";
echo "endif count: ", substr_count($out, "endif;"), "\n";
