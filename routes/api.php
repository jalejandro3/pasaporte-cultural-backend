<?php

use App\Http\Controllers\ParticipationController;
use Illuminate\Support\Facades\Route;

Route::post('/participations', [ParticipationController::class, 'store']);
