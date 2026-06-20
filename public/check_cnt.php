<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
echo App\Domains\Expenses\Models\Expense::count() . "|" . App\Domains\Expenses\Models\Expense::latest('id')->first()->amount;
