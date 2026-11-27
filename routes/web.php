<?php

use App\Http\Controllers\Adviser\MemberController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WelcomeController;
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
        Route::middleware('role:member')->group(function () {
            // First sign-in setup: codename, track, avatar.
            Route::get('/welcome', [WelcomeController::class, 'edit'])->name('welcome.edit');
            Route::put('/welcome', [WelcomeController::class, 'update'])->name('welcome.update');

            // Everything below needs the welcome setup finished first.
            Route::middleware('profile.done')->group(function () {
                Route::get('/', [HomeController::class, 'index'])->name('home');
            });
        });

        // Adviser pages
        Route::middleware('role:adviser')->prefix('adviser')->name('adviser.')->group(function () {
            Route::get('/', [AdviserController::class, 'index'])->name('home');
            Route::get('/members', [MemberController::class, 'index'])->name('members.index');
            Route::post('/members', [MemberController::class, 'store'])->name('members.store');
            // {member} is the member's id number; Laravel looks up the User for us.
            Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
            Route::put('/members/{member}/password', [MemberController::class, 'resetPassword'])->name('members.password');
            Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        });
    });
});
