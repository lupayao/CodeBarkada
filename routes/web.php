<?php

use App\Http\Controllers\AdviserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Pages for visitors who are NOT signed in.
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    // At most 5 sign-in tries per minute, to stop password guessing.
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

// Pages for signed-in users. "auth.session" signs out other devices after a password change.
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // Everything below needs the default password changed first.
    Route::middleware('password.changed')->group(function () {
        // Member pages
        Route::get('/', [HomeController::class, 'index'])->middleware('role:member')->name('home');

        // Adviser pages
        Route::get('/adviser', [AdviserController::class, 'index'])->middleware('role:adviser')->name('adviser.home');
    });
});
