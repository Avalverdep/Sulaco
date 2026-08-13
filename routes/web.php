<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'tienda' => 'Sulaco',
    ]);
});

Route::get('/eventos', function () {
    return Inertia::render('Eventos', [
        'proximos' => ['Torneo Warhammer 40k', 'Liga Pokémon TCG'],
    ]);
});