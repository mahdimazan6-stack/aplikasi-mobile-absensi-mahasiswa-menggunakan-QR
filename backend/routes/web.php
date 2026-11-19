<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\DivisiController;
use App\Http\Controllers\Admin\KehadiranController;
use App\Http\Controllers\Admin\IzinController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;

// login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// redirect awal
Route::get('/', function () {
    return redirect('/login');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('/karyawan', KaryawanController::class);
    Route::resource('/divisi', DivisiController::class);
    Route::resource('/kehadiran', KehadiranController::class);

    // IZIN (JANGAN resource)
    Route::get('/izin', [IzinController::class, 'index'])
        ->name('izin.index');

    Route::post('/izin/{index}/approve', [IzinController::class, 'approve'])
        ->name('izin.approve');

    Route::post('/izin/{index}/reject', [IzinController::class, 'reject'])
        ->name('izin.reject');

});
Route::prefix('user')->name('user.')->group(function () {

    // PAGE
    Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/izin', [UserDashboardController::class, 'izin'])
        ->name('izin');

    Route::get('/laporan', [UserDashboardController::class, 'laporan'])
        ->name('laporan');

    // ACTION
    Route::post('/check-in', [UserDashboardController::class, 'checkIn'])
        ->name('checkin');

    Route::post('/check-out', [UserDashboardController::class, 'checkOut'])
        ->name('checkout');

    Route::post('/reset-absen', [UserDashboardController::class, 'resetAbsen'])
        ->name('reset');
    Route::get('/izin', [UserDashboardController::class, 'izin'])->name('izin');
    Route::post('/izin', [UserDashboardController::class, 'storeIzin'])->name('izin.store');
    
});


// --------------------------------------------------------
// ROUTE AUTH (Wajib login buat akses)
// --------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // KHUSUS ADMIN
    
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('/karyawan', KaryawanController::class);
        Route::put('/karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword'])->name('karyawan.reset-password');
        Route::resource('/divisi', DivisiController::class);
        Route::resource('/kehadiran', KehadiranController::class);

        // IZIN (Custom, bukan resource)
        Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
        Route::post('/izin/{index}/approve', [IzinController::class, 'approve'])->name('izin.approve');
        Route::post('/izin/{index}/reject', [IzinController::class, 'reject'])->name('izin.reject');
    });


    // ==========================================
    // KHUSUS USER / KARYAWAN
    // ==========================================
    Route::prefix('user')->name('user.')->middleware('role:user')->group(function () {

        // PAGE
        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/laporan', [UserDashboardController::class, 'laporan'])->name('laporan');

        // IZIN (Page & Action)
        Route::get('/izin', [UserDashboardController::class, 'izin'])->name('izin');
        Route::post('/izin', [UserDashboardController::class, 'storeIzin'])->name('izin.store');

        // ACTION ABSENSI
        Route::post('/check-in', [UserDashboardController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [UserDashboardController::class, 'checkOut'])->name('checkout');
        Route::post('/reset-absen', [UserDashboardController::class, 'resetAbsen'])->name('reset');

    });

});
