<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Init first tenant
$tenant = App\Models\Tenant::first();
if($tenant) {
    tenancy()->initialize($tenant);
    $reconductions = App\Models\ProgrammaticReconduction::with('items')->get();
    echo "Found " . $reconductions->count() . " reconductions.\n";
    foreach($reconductions as $r) {
        echo "Reconduction " . $r->id . " has " . $r->items->count() . " items.\n";
    }
} else {
    echo "No tenants found.\n";
}
