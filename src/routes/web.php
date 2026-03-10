<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/get-online-characters', [HomeController::class, 'getOnlineCharacters'])->name('get-online-characters');
Route::get('/refil', [HomeController::class, 'refil'])->name('refil');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [HomeController::class, 'index'])->name('admin.home');
    Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
    Route::post('/settings', [HomeController::class, 'saveSettings'])->name('settings.save');
    Route::post('/set/{characterName}/as/{type}', [HomeController::class, 'setCharacterType'])->name('update.character.type');
    Route::post('/set/{characterName}/as/attacker/{isAttacker}', [HomeController::class, 'setCharacterAsAttacker'])->name('update.character.attacker');
    Route::post('/position/{characterName}', [HomeController::class, 'updateCharacterPosition'])->name('update.character.position');
    Route::get('/online-graphics-gant', [HomeController::class, 'getCharactersOnlineGantGraphics'])->name('online-graphics-gant');
});
