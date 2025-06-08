<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GptRequestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/start', function () {
    return Inertia::render('Start');
})->name('start');

Route::get('/lk', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // GPT Routes
    Route::get('/gpt', [GptRequestController::class, 'index'])->name('gpt');
    Route::post('/gpt-requests', [GptRequestController::class, 'store'])->name('gpt.store');
    Route::get('/gpt-requests', [GptRequestController::class, 'getRequests'])->name('gpt.requests');
    Route::get('/gpt-requests/{gptRequest}', [GptRequestController::class, 'show'])->name('gpt.show');
});

require __DIR__.'/auth.php';
