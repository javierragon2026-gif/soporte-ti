<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

for($i = 1; $i <= 10; $i++) {
    \App\Models\Device::firstOrCreate([
        'serial_number' => 'HO-LAP-' . str_pad($i, 2, '0', STR_PAD_LEFT)
    ], [
        'name' => 'Laptop Préstamo HO ' . $i,
        'type' => 'shared',
        'status' => 'available'
    ]);
}
echo "Created 10 laptops\n";

