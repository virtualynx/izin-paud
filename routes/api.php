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

    Route::get('/list', [PermitApi::class, 'list']);
    Route::get('/list_document/{req_id}', [PermitApi::class, 'list_document']);

    Route::post('/dt_to_verify_list', [PermitApi::class, 'dt_to_verify_list']);
    Route::post('/request_submit', [PermitApi::class, 'request_submit']);

    Route::prefix('revision_notes')->group(function () {
        Route::get('/list/{req_id}', [PermitApi::class, 'revision_notes_list']);
        Route::post('/update', [PermitApi::class, 'revision_notes_update']);
    });

    Route::post('/reqdoc_update', [PermitApi::class, 'reqdoc_update']);
    Route::post('/request_update', [PermitApi::class, 'request_update']);
});