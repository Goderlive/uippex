<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create master tables
        Schema::create('master_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('master_administrative_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_department_id')->constrained('master_departments')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Alter transactional tables
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('master_department_id')->nullable()->constrained('master_departments')->cascadeOnDelete();
        });

        Schema::table('administrative_units', function (Blueprint $table) {
            $table->foreignId('master_administrative_unit_id')->nullable()->constrained('master_administrative_units')->cascadeOnDelete();
        });

        // 3. Alter users table to add new relationships
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('master_department_id')->nullable()->constrained('master_departments')->nullOnDelete();
            $table->foreignId('master_administrative_unit_id')->nullable()->constrained('master_administrative_units')->nullOnDelete();
        });

        // 4. Data patching within a transaction
        DB::transaction(function () {
            $departments = DB::table('departments')->get();
            $masterDeptMap = [];

            foreach ($departments as $dept) {
                if (!isset($masterDeptMap[$dept->name])) {
                    $masterDeptId = DB::table('master_departments')->insertGetId([
                        'name' => $dept->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $masterDeptMap[$dept->name] = $masterDeptId;
                } else {
                    $masterDeptId = $masterDeptMap[$dept->name];
                }

                DB::table('departments')->where('id', $dept->id)->update([
                    'master_department_id' => $masterDeptId
                ]);

                DB::table('users')->where('department_id', $dept->id)->update([
                    'master_department_id' => $masterDeptId
                ]);
            }

            $adminUnits = DB::table('administrative_units')->get();
            $masterAdminUnitMap = [];

            foreach ($adminUnits as $unit) {
                $dept = DB::table('departments')->where('id', $unit->department_id)->first();
                $masterDeptId = $dept ? $dept->master_department_id : null;

                if ($masterDeptId) {
                    $mapKey = $unit->name . '_' . $masterDeptId;
                    if (!isset($masterAdminUnitMap[$mapKey])) {
                        $masterAdminId = DB::table('master_administrative_units')->insertGetId([
                            'master_department_id' => $masterDeptId,
                            'name' => $unit->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $masterAdminUnitMap[$mapKey] = $masterAdminId;
                    } else {
                        $masterAdminId = $masterAdminUnitMap[$mapKey];
                    }

                    DB::table('administrative_units')->where('id', $unit->id)->update([
                        'master_administrative_unit_id' => $masterAdminId
                    ]);

                    DB::table('users')->where('administrative_unit_id', $unit->id)->update([
                        'master_administrative_unit_id' => $masterAdminId
                    ]);
                }
            }
        });

        // 5. Drop old columns in users
        Schema::table('users', function (Blueprint $table) {
            // Note: Since constraints might be named differently depending on exact creation, 
            // array syntax automatically handles the default naming convention.
            $table->dropForeign(['department_id']);
            $table->dropForeign(['administrative_unit_id']);
            $table->dropColumn(['department_id', 'administrative_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recreate old columns in users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('administrative_unit_id')->nullable()->constrained('administrative_units')->nullOnDelete();
        });

        // We attempt a best-effort rollback of user data to the most recent transaction tables
        DB::transaction(function () {
            $users = DB::table('users')->get();
            foreach ($users as $user) {
                if ($user->master_department_id) {
                    // Find the most recent department linked to this master
                    $dept = DB::table('departments')->where('master_department_id', $user->master_department_id)->orderBy('id', 'desc')->first();
                    if ($dept) {
                        DB::table('users')->where('id', $user->id)->update(['department_id' => $dept->id]);
                    }
                }
                if ($user->master_administrative_unit_id) {
                    $unit = DB::table('administrative_units')->where('master_administrative_unit_id', $user->master_administrative_unit_id)->orderBy('id', 'desc')->first();
                    if ($unit) {
                        DB::table('users')->where('id', $user->id)->update(['administrative_unit_id' => $unit->id]);
                    }
                }
            }
        });

        // 2. Drop new columns and tables
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['master_department_id']);
            $table->dropForeign(['master_administrative_unit_id']);
            $table->dropColumn(['master_department_id', 'master_administrative_unit_id']);
        });

        Schema::table('administrative_units', function (Blueprint $table) {
            $table->dropForeign(['master_administrative_unit_id']);
            $table->dropColumn('master_administrative_unit_id');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['master_department_id']);
            $table->dropColumn('master_department_id');
        });

        Schema::dropIfExists('master_administrative_units');
        Schema::dropIfExists('master_departments');
    }
};
