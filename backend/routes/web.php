<?php

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\IzinController;
use App\Http\Controllers\Admin\KehadiranController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use Illuminate\Support\Facades\Route;

// login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// redirect awal
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('/pengguna', UserManagementController::class)
            ->only(['index', 'store', 'update'])
            ->parameters(['pengguna' => 'user']);
        Route::put('/pengguna/{user}/password', [UserManagementController::class, 'resetPassword'])->name('pengguna.password');
        Route::patch('/pengguna/{user}/status', [UserManagementController::class, 'toggleStatus'])->name('pengguna.status');
        Route::resource('/mata-kuliah', CourseController::class)
            ->except(['show'])
            ->parameters(['mata-kuliah' => 'course']);
        Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');

        Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
        Route::post('/izin/{leaveRequest}/approve', [IzinController::class, 'approve'])->name('izin.approve');
        Route::post('/izin/{leaveRequest}/reject', [IzinController::class, 'reject'])->name('izin.reject');
        Route::get('/izin/{leaveRequest}/attachment', [IzinController::class, 'downloadAttachment'])->name('izin.attachment');
    });

    Route::prefix('user')->name('user.')->middleware('role:mahasiswa,dosen')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');
    });

    Route::prefix('user')->name('user.')->middleware('role:mahasiswa')->group(function () {
        Route::get('/laporan', [UserDashboardController::class, 'laporan'])->name('laporan');
        Route::get('/izin', [UserDashboardController::class, 'izin'])->name('izin');
        Route::post('/izin', [UserDashboardController::class, 'storeIzin'])->name('izin.store');
        Route::get('/izin/{leaveRequest}/attachment', [UserDashboardController::class, 'downloadAttachment'])->name('izin.attachment');
    });
});
