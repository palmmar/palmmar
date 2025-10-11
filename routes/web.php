<?php

    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\Landlord\TenantAdminController;


    Route::middleware('tenant')->group(function () {
        require __DIR__.'/auth.php';

        Route::middleware('auth')->group(function () {
            require __DIR__.'/users.php';
            Route::get('/', fn () => redirect()->route('users.index'))->name('home');
        });
    });


    Route::prefix('landlord')->name('landlord.')->group(function () {
        Route::get('/',               [TenantAdminController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [TenantAdminController::class, 'create'])->name('tenants.create');
        Route::post('/tenants',       [TenantAdminController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}/provision', [TenantAdminController::class, 'provision'])->name('tenants.provision');
    });
