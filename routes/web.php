<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/get-online-characters', [HomeController::class, 'getOnlineCharacters']);
Route::post('/set/{characterName}/as/{type}', [HomeController::class, 'setCharacterType']);
Route::get('/online-graphics-gant', [HomeController::class, 'getCharactersOnlineGantGraphics']);
