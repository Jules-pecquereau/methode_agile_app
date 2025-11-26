<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['manager'])->group(function () {
    Route::resource('tasks', TaskController::class)->except(['destroy', 'show']);
    Route::patch('tasks/{task}/deactivate', [TaskController::class, 'deactivate'])->name('tasks.deactivate');
});

Route::middleware(['auth', 'manager'])->group(function () {
    Route::resource('tasks', TaskController::class)->except(['destroy', 'show']);
    Route::patch('tasks/{task}/deactivate', [TaskController::class, 'deactivate'])->name('tasks.deactivate');
});

require __DIR__.'/auth.php';
