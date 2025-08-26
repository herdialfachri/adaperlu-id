<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/password-reset/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request('email')
    ]);
})->name('password.reset');