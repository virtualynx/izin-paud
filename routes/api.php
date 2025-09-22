<?php

use App\Http\Api\PermitApi;
use App\Http\Api\RateApi;
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

    Route::post('/dt_request_list', [PermitApi::class, 'dt_request_list']);
    Route::post('/dt_request_officer_list', [PermitApi::class, 'dt_request_officer_list']);
    Route::post('/request_submit', [PermitApi::class, 'request_submit']);

    Route::prefix('revision_notes')->group(function () {
        Route::get('/list/{req_id}', [PermitApi::class, 'revision_notes_list']);
        Route::post('/update', [PermitApi::class, 'revision_notes_update']);
    });

    Route::get('/revision_documents/list/{req_id}', [PermitApi::class, 'revision_documents_list']);

    Route::post('/reqdoc_update', [PermitApi::class, 'reqdoc_update']);
    Route::post('/reqdoc_info', [PermitApi::class, 'reqdoc_info']);
    Route::post('/request_update', [PermitApi::class, 'request_update']);
    Route::post('/decree_upload', [PermitApi::class, 'decree_upload']);
});

Route::prefix('rate')->group(function () {
    Route::get('/get/{req_id}', [RateApi::class, 'get']);
    Route::post('/send', [RateApi::class, 'send']);
});