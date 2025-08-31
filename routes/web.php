<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::prefix('sso')->group(function () {
    Route::get('/login', [SsoController::class, 'loginPage']);
    Route::get('/callback', [SsoController::class, 'callback']);
    Route::get('/logout', [SsoController::class, 'logout']);
});

Route::middleware(['sso_login'])->group(function () {
    Route::prefix('permit')->group(function () {
        Route::get('/request', [PermitController::class, 'page_request']);
        Route::get('/verification', [PermitController::class, 'page_verification']);
        Route::get('/approval', [PermitController::class, 'page_approval']);
    });
});
