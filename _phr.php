<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Models\StatusPhrase::where('status', 'approved')->first() ?? App\Models\StatusPhrase::first();
echo 'PHRASE_ID=' . ($p->id ?? 'NONE') . '|CAT=' . ($p->category ?? 'amor');
