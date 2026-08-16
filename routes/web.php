<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', [LandingController::class, 'index']);

Route::get('/eventos', function () {
    return Inertia::render('Eventos', [
        'proximos' => ['Torneo Warhammer 40k', 'Liga Pokémon TCG'],
    ]);
});

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);