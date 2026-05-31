<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$posts = \App\Models\BlogPost::all();
foreach ($posts as $post) {
    echo "ID: " . $post->id . " | Title: " . $post->title . "\n";
}
