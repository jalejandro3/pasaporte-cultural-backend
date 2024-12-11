<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminUser;
use App\Http\Middleware\Jwt;
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
 * AUTH
 */
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/refresh-token', [AuthController::class, 'refreshToken']);
Route::middleware('validate.domain')->post('auth/register', [AuthController::class, 'register']);
Route::middleware('jwt')->group(function () {
    /**
     * USERS
     */
    Route::get('users/profile', [UserController::class, 'profile']);
    Route::put('users/profile', [UserController::class, 'updateProfile']);
    Route::middleware('admin.user')->group(function () {
        Route::put('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);
    });
});