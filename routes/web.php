<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ToDoItemController;
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

    Route::get('/todos', [ToDoItemController::class, 'index'])->name('todos.index');
    Route::resource('todos', ToDoItemController::class);

    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::resource('todo', ToDoItemController::class);

    Route::patch('/todos/{id}/toggle-complete', [ToDoItemController::class, 'toggleComplete'])->name('todos.toggleComplete');

    Route::delete('/todos/{id}/destroy', [ToDoItemController::class, 'destroy'])->name('todos.destroy');
    Route::patch('/todos/{id}/restore', [ToDoItemController::class, 'restore'])->name('todos.restore');

});

require __DIR__.'/auth.php';
