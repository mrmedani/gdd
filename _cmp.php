<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    \Blade::compileString(file_get_contents("resources/views/livewire/station-charges.blade.php"));
    echo "compileString OK\n";
} catch (Throwable $e) {
    echo "ERREUR COMPILE: ", mb_substr($e->getMessage(), 0, 260), "\n";
    echo "file: ", $e->getFile(), ":", $e->getLine(), "\n";
}
