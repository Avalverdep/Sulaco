<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', [LandingController::class, 'index']);

Route::get('/eventos', [\App\Http\Controllers\EventCalendarController::class, 'index']);

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    Route::get('/eventos', [\App\Http\Controllers\Admin\EventController::class, 'index'])->name('eventos.index');
});