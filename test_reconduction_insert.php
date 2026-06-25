<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = App\Models\Tenant::first();
if($tenant) {
    tenancy()->initialize($tenant);
    
    // Find the last reconduction
    $reconduction = App\Models\ProgrammaticReconduction::latest('id')->first();
    if($reconduction) {
        $act = App\Models\SubstantiveActivity::where('administrative_unit_id', $reconduction->administrative_unit_id)->first();
        if ($act) {
            $reconduction->items()->create([
                'substantive_activity_id' => $act->id,
                'modification_type' => 'increase',
                'previous_annual_goal' => 10,
                'new_annual_goal' => 20,
                'achieved_so_far' => 5,
                'previous_schedule' => ['jan'=>1],
                'new_schedule' => ['jan'=>2],
                'justification' => 'Test save'
            ]);
            echo "Successfully inserted item into ReconductionItem for Reconduction " . $reconduction->id . "!\n";
            echo "Total items now: " . $reconduction->items()->count() . "\n";
        } else {
            echo "No activities found for this area.\n";
        }
    }
}
