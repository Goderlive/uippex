<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return Inertia\Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'tenantId' => tenant('id'),
        ]);
    });

    Route::get('/dashboard', function () {
        return Inertia\Inertia::render('Dashboard', [
            'tenantId' => tenant('id')
        ]);
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::middleware(['role:Super-Admin|PMD-Planeación'])->group(function () {
            Route::resource('users', App\Http\Controllers\UserController::class);
            
            if (file_exists(base_path('routes/premium.php'))) {
                require base_path('routes/premium.php');
            }
        });

        // Core Operativo: Actividades y Seguimiento
        Route::prefix('actividades')->group(function () {
            Route::get('/', [App\Http\Controllers\Tenant\ActivityWorkflowController::class, 'index'])->name('activities.index');
            Route::get('/dependencia/{department}', [App\Http\Controllers\Tenant\ActivityWorkflowController::class, 'showDepartment'])->name('activities.department.show');
            Route::get('/area/{administrativeUnit}', [App\Http\Controllers\Tenant\ActivityWorkflowController::class, 'showArea'])->name('activities.area.show');
            Route::post('/progreso/{activity}', [App\Http\Controllers\Tenant\ActivityWorkflowController::class, 'storeProgress'])->name('activities.progress.store');
            Route::post('/progreso-validar/{report}', [App\Http\Controllers\Tenant\ActivityWorkflowController::class, 'validateProgress'])->name('activities.progress.validate');
        });

        // Ajustes (Role: Super-Admin & PMD-Planeación)
        Route::middleware(['role:Super-Admin|PMD-Planeación'])->prefix('ajustes')->group(function () {
            Route::prefix('dependencias')->name('departments.')->group(function () {
                Route::get('/', [App\Http\Controllers\Tenant\DepartmentController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\Tenant\DepartmentController::class, 'store'])->name('store');
                Route::put('/{department}', [App\Http\Controllers\Tenant\DepartmentController::class, 'update'])->name('update');
                Route::delete('/{department}', [App\Http\Controllers\Tenant\DepartmentController::class, 'destroy'])->name('destroy');
            });
            
            Route::prefix('areas')->name('areas.')->group(function () {
                Route::put('/{area}', [App\Http\Controllers\Tenant\DepartmentController::class, 'updateArea'])->name('update');
                Route::post('/{area}/move', [App\Http\Controllers\Tenant\DepartmentController::class, 'moveArea'])->name('move');
            });
        });

        // Reconducciones OSFEM
        Route::prefix('reconducciones')->name('reconductions.')->group(function () {
            Route::get('/', [App\Http\Controllers\Tenant\ReconductionController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Tenant\ReconductionController::class, 'store'])->name('store');
            Route::get('/{reconduction}/edit', [App\Http\Controllers\Tenant\ReconductionController::class, 'edit'])->name('edit');
            Route::put('/{reconduction}', [App\Http\Controllers\Tenant\ReconductionController::class, 'update'])->name('update');
            Route::post('/{reconduction}/submit', [App\Http\Controllers\Tenant\ReconductionController::class, 'sendToValidation'])->name('submit');
            Route::post('/{reconduction}/approve', [App\Http\Controllers\Tenant\ReconductionController::class, 'approveReconduction'])->name('approve');
            
            // OSFEM PDF Download Route
            Route::get('/{reconduction}/dictamen-pdf', [App\Http\Controllers\Tenant\ReconductionController::class, 'generatePdf'])->name('pdf');
        });
    });

    require __DIR__.'/auth.php';
});
