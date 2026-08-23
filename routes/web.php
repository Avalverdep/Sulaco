<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\EventController;

Route::get('/', [LandingController::class, 'index']);

Route::get('/eventos', [\App\Http\Controllers\EventCalendarController::class, 'index']);

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    Route::get('/eventos', [\App\Http\Controllers\Admin\EventController::class, 'index'])->name('eventos.index');

    Route::get('/eventos', [EventController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/crear', [EventController::class, 'create'])->name('eventos.create');
    Route::post('/eventos', [EventController::class, 'store'])->name('eventos.store');
    Route::delete('/eventos/{evento}', [EventController::class, 'destroy'])->name('eventos.destroy');
    Route::patch('/eventos/{evento}/cancelar', [EventController::class, 'cancel'])->name('eventos.cancel');
});

Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/', fn () => Inertia::render('User/Index'))->name('index');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');