<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$post = \App\Models\BlogPost::where('title', 'testesto')->first();
if ($post) {
    echo "Found post: " . $post->id . "\n";
    $post->delete();
    echo "Deleted.\n";
} else {
    echo "Not found.\n";
}
