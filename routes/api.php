<?php

use App\Http\Api\PermitApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('permit')->group(function () {
    Route::prefix('docrec')->group(function () {
        Route::get('/list', [PermitApi::class, 'docrec_list']);
    });
});