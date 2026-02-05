<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::as('api.')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    Route::prefix('properties')->as('properties.')->group(function () {
        Route::get('search', [\App\Http\Controllers\Api\PropertyController::class, 'search'])->name('search');
        Route::post('seed', [\App\Http\Controllers\Api\PropertyController::class, 'seed'])->name('seed');
        Route::post('clean', [\App\Http\Controllers\Api\PropertyController::class, 'clean'])->name('clean');
        Route::post('import', [\App\Http\Controllers\Api\PropertyController::class, 'import'])->name('import');
    });

});

