<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home');
})->name('home');

Route::get('/about', fn () => Inertia::render('about'))->name('about');
Route::get('/connection', fn () => Inertia::render('connection'))->name('connection');
Route::get('/products', fn () => Inertia::render('products'))->name('products');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
