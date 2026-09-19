<?php

use App\Http\Controllers\Pengaju\DashboardController;
use App\Http\Controllers\Pengaju\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Pengaju Routes
    Route::middleware('role:pengaju')
        ->prefix('pengaju')
        ->name('pengaju.')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        });

    // Admin Kesra Routes
    Route::middleware('role:admin-kesra')
        ->prefix('admin-kesra')
        ->name('admin-kesra.')
        ->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\AdminKesra\DashboardController::class, 'index'])->name('dashboard');
        });

    // Super Admin Routes
    Route::middleware('role:super_admin')
        ->prefix('super-admin')
        ->name('super-admin.')
        ->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
        });
});

require __DIR__.'/auth.php';
