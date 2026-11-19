<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('bearer')->group(function () {
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/courses', [AttendanceApiController::class, 'courses']);
    Route::post('/attendance/sessions', [AttendanceApiController::class, 'createSession']);
    Route::get('/attendance/sessions/{session}/attendances', [AttendanceApiController::class, 'sessionAttendances']);
    Route::post('/attendance/sessions/{session}/end', [AttendanceApiController::class, 'endSession']);
    Route::get('/attendance/courses/{course}/attendances', [AttendanceApiController::class, 'courseAttendances']);
    Route::get('/attendance/history', [AttendanceApiController::class, 'history']);
    Route::post('/attendance/scan', [AttendanceApiController::class, 'scan']);
});
