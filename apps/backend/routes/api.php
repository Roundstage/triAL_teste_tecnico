<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\EnsureUsuarioAtivo;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::middleware(['auth:api', EnsureUsuarioAtivo::class])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
