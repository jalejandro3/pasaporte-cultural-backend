<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * ACTIVITIES
 */
Route::controller(ActivityController::class)->group(function() {
    Route::get('activities/enrolled', 'getEnrolledActivities');
    Route::get('activities/{id}', 'show')->where('id', '[0-9]+');
    Route::get('activities', 'getAllActivities');
    Route::post('activities/register', 'register')->middleware('validate.qr');

    Route::middleware('admin.user')->group(function () {
        Route::post('activities', 'create');

        /**
         * ADMINISTRATIVE
         */
        Route::get('activities/attendance', 'getActivityAttendance');
    });
})->middleware('jwt');

/**
 * AUTH
 */
Route::controller(AuthController::class)->group(function() {
    Route::post('auth/login', 'login');
    Route::post('auth/register', 'register')->middleware(['validate.domain', 'validate.role']);

    /**
     * PASSWORD
     */
    Route::post('auth/forgot-password', 'forgotPassword');
    Route::post('auth/reset-password', 'resetPassword');

    /**
     * TOKEN
     */
    Route::post('auth/refresh-token', 'refreshToken');
    Route::post('auth/validate-token', 'validateToken');
});

/**
 * LOCATIONS
 */
Route::controller(LocationController::class)->group(function () {
    Route::get('countries', 'getCountries');
    Route::get('countries/{id}/cities', 'getCities');
})->middleware('jwt');

/**
 * QR CODE
 */
Route::controller(QrCodeController::class)->group(function () {
    Route::post('qr-code/regenerate', 'regenerateCode');
})->middleware(['jwt', 'admin.user']);

/**
 * USERS
 */
Route::controller(UserController::class)->group(function () {
    Route::get('users/profile', 'profile');
    Route::put('users/profile', 'updateProfile')->middleware('validate.domain');

    Route::middleware('admin.user')->group(function () {
        Route::get('users', 'getAllUsers');
        Route::put('users/{id}', 'updateRole');
        Route::delete('users/{id}', 'destroy');
    });
})->middleware('jwt');
