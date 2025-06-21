<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\KanbanListController;
use App\Http\Controllers\CardController;

Route::get('/', function () {
    return redirect()->route('boards.index');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Test route for debugging
Route::put('/test-put', function () {
    return response()->json(['message' => 'PUT request works']);
})->middleware('auth');

// Kanban Routes
Route::middleware('auth')->group(function () {
    Route::resource('boards', BoardController::class);
    
    // List Routes
    Route::post('boards/{board}/lists', [KanbanListController::class, 'store'])->name('lists.store');
    Route::put('lists/{list}', [KanbanListController::class, 'update'])->name('lists.update');
    Route::delete('lists/{list}', [KanbanListController::class, 'destroy'])->name('lists.destroy');
    Route::post('lists/reorder', [KanbanListController::class, 'reorder'])->name('lists.reorder');
    
    // Card Routes
    Route::post('lists/{list}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::get('cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::put('cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::post('cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::post('cards/reorder', [CardController::class, 'reorder'])->name('cards.reorder');
});
