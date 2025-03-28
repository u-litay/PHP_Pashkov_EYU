<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index']);
Route::get('/games', [GameController::class, 'getGames']);
Route::post('/games', [GameController::class, 'startGame']);
Route::post('/step/{id}', [GameController::class, 'submitAnswer']);
Route::post('/clear', [GameController::class, 'clearDatabase']);
