<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::as('api.')->middleware('auth:sanctum', 'api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    Route::prefix('properties')->as('properties.')->group(function () {
        Route::get('/search', [\App\Http\Controllers\Api\PropertyController::class, 'search'])->name('search');
    });

});

