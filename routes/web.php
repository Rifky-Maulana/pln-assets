<?php

use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


//laporan
Route::middleware(['auth'])->group(function () {
    Route::get('/riwayat', [ReportController::class, 'index'])->name('riwayat.index');
    Route::post('/riwayat', [ReportController::class, 'store'])->name('riwayat.store');
    Route::get('/riwayat/{id}', [ReportController::class, 'show'])->name('riwayat.show');
    Route::put('/riwayat/{riwayat}', [ReportController::class, 'update'])->name('riwayat.update');
    Route::delete('/riwayat/{riwayat}', [ReportController::class, 'destroy'])->name('riwayat.destroy');
});

// Group untuk route yang membutuhkan autentikasi dan verifikasi email
Route::middleware(['auth', 'verified'])->group(function () {
    // Route untuk user biasa
    Route::get('/dashboard', function () {
        return Inertia::render('Home');
    })->name('dashboard');

    // Route khusus admin dengan middleware role:admin
    Route::middleware([CheckRole::class . ':admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');
    });

    // Route untuk form laporan
    Route::get('/form', function () {
        return Inertia::render('FormLaporan');
    })->name('form');

    // Route untuk manajemen asset
    Route::resource('assets', AssetController::class);

    // Route untuk profile user
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
