<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
// Rendu du commit reel avec session user + locale ar (la prod-like)
echo "=== rendu blade avec props de montagne ===
";
$c = app(App\Domains\Treasury\Livewire\StationChargesIndex::class);
$c->mount();
$r = $c->render();
// extraire le fragment STATUT du composant sitemap par recherche textual
echo get_class($r), " view\n";
