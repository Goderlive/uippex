<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = App\Models\Tenant::first();
if($tenant) {
    tenancy()->initialize($tenant);
    $acts = App\Models\SubstantiveActivity::all();
    echo "Found " . $acts->count() . " activities.\n";
}
