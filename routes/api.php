<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
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
    Route::get('activities/{id}', 'show');
    Route::post('activities', 'create')->middleware('validate.role');
});

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
