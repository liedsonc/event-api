<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect /login to event owner login (most common use case)
Route::get('/login', function () {
    return redirect('/event-owner/login');
})->name('login');
