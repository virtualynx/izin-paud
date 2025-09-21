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
        Route::get('/edit/{req_id}', [PermitController::class, 'page_edit']);
        Route::get('/request_list', [PermitController::class, 'request_list']);
        Route::get('/verify_list', [PermitController::class, 'verify_list']);
        Route::get('/approval_list', [PermitController::class, 'approval_list']);
        Route::get('/decree_list', [PermitController::class, 'decree_list']);
        
        Route::get('/verify/{req_id}', [PermitController::class, 'page_verify']);
        Route::get('/revision/{req_id}', [PermitController::class, 'page_revision']);
        
        Route::prefix('document')->group(function () {
            Route::get('/preview/{req_doc_id}', [PermitController::class, 'document_preview']);
        });
        Route::prefix('decree')->group(function () {
            Route::get('/preview/{permit_decree_id}', [PermitController::class, 'decree_preview']);
        });
    });
});
