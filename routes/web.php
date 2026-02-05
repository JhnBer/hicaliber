<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'propertyCount' => \App\Models\Property::query()->count(),
        'importActive' => Illuminate\Support\Facades\Cache::get('properties_import', false),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::prefix('properties')->as('properties.')->group(function () {
    Route::get('', [\App\Http\Controllers\PropertyController::class, 'index'])->name('index');
});


require __DIR__.'/settings.php';
