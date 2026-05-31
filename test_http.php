<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/admin/blog/35', 'DELETE');
// set session to bypass csrf
$request->setSession($app['session']->driver('array'));
// auth?
$app['auth']->loginUsingId(1); // Assuming ID 1 is admin

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
