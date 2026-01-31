<?php

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
});
