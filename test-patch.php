<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'like', 'aux.logistica%')->first();
$user->name = "Anahí Bonilla Temp";
$user->save();
echo "Temp saved\n";
sleep(2);
$user->name = "Anahí Bonilla";
$user->save();
echo "Final saved\n";
