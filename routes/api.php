<?php

use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\PracticeSessionController;
use App\Http\Controllers\Api\RecordingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;

Route::prefix("/v1")->group(function (){
    Route::prefix('auth')->group(function () {

        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',    [AuthController::class, 'login']);
        Route::post('/forgot-password', [PasswordController::class, 'forgot']);
        Route::post('/reset-password',  [PasswordController::class, 'reset']);
    
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::get('categories', [ChallengeController::class, 'categories']);
    Route::get('levels', [ChallengeController::class, 'levels']);
    Route::get('challenges', [ChallengeController::class, 'index']);
    Route::get('challenges/{challenge}', [ChallengeController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('practice-sessions', [PracticeSessionController::class, 'start']);
        Route::get('practice-sessions', [PracticeSessionController::class, 'index']);
        Route::get('practice-sessions/{session}', [PracticeSessionController::class, 'show']);
        Route::post('recordings', [RecordingController::class, 'store']);
    });
});
