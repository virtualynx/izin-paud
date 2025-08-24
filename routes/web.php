<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::prefix('sso')->group(function () {
    Route::get('/callback', [SsoController::class, 'callback']);
});

Route::middleware(['sso_login'])->group(function () {
    Route::prefix('permit')->group(function () {
        // Route::get('/', [DocumentController::class, 'index']);
        // Route::get('/upload', [DocumentController::class, 'view_upload']);
    });
});
