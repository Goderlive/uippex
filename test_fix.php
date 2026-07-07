<?php

tenancy()->initialize('calimaya');

$depts = App\Models\Department::whereNull('master_department_id')->get();
foreach ($depts as $dept) {
    $master = App\Models\MasterDepartment::create(['name' => $dept->name]);
    $dept->master_department_id = $master->id;
    $dept->saveQuietly();
    echo "Fixed Dept: " . $dept->name . "\n";
}

$units = App\Models\AdministrativeUnit::whereNull('master_administrative_unit_id')->get();
foreach ($units as $unit) {
    $dept = $unit->department;
    $master = App\Models\MasterAdministrativeUnit::create([
        'name' => $unit->name,
        'master_department_id' => $dept ? $dept->master_department_id : null
    ]);
    $unit->master_administrative_unit_id = $master->id;
    $unit->saveQuietly();
    echo "Fixed Unit: " . $unit->name . "\n";
}

// Fix users if they picked a department that just got fixed
$users = App\Models\User::whereNull('master_department_id')->get();
foreach ($users as $user) {
    // We cannot automatically know which dept they selected if they already saved it as null,
    // but the user can just edit them in the UI.
}

echo "Done.\n";
