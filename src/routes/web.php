<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/get-online-characters', [HomeController::class, 'getOnlineCharacters']);

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [HomeController::class, 'index'])->name('home');
    Route::post('/set/{characterName}/as/{type}', [HomeController::class, 'setCharacterType']);
    Route::post('/set/{characterName}/as/attacker/{isAttacker}', [HomeController::class, 'setCharacterAsAttacker']);
    Route::post('/position/{characterName}', [HomeController::class, 'updateCharacterPosition']);
    Route::get('/online-graphics-gant', [HomeController::class, 'getCharactersOnlineGantGraphics'])->name('online-graphics-gant');
});
