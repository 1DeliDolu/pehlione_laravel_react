<?php

use App\Http\Controllers\DocumentationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home');
})->name('home');

Route::get('/about', fn () => Inertia::render('about'))->name('about');
Route::get('/connection', fn () => Inertia::render('connection'))->name('connection');
Route::get('/products', fn () => Inertia::render('products'))->name('products');

Route::prefix('docs')->group(function () {
    Route::get('/', [DocumentationController::class, 'index'])->name('docs.index');
    Route::get('{section}', [DocumentationController::class, 'section'])->name('docs.section');
    Route::get('{section}/{document}', [DocumentationController::class, 'show'])->name('docs.show');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
