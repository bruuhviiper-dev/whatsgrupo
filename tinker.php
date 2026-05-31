<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\StatusPhrase::whereNull('hash')->get()->each(function($p) {
    $p->hash = \Illuminate\Support\Str::random(10);
    $p->save();
});

echo "Hashes populated successfully.";
