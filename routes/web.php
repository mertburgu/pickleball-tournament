<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TournamentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('tournament')->namespace('Tournament')->group(function () {
    Route::get('/', [TournamentController::class, 'index'])->name('tournament.index');
    Route::get('/show/{tournament}', [TournamentController::class, 'show'])->name('tournament.show');
    Route::get('/create', [TournamentController::class, 'create'])->name('tournament.create');
    Route::post('/create',  [TournamentController::class, 'store'])->name('tournament.store');
    Route::get('/edit/{tournament}', [TournamentController::class, 'edit'])->name('tournament.edit');
    Route::put('/update/{tournament}',  [TournamentController::class, 'update'])->name('tournament.update');
    Route::delete('/delete/{tournament}',  [TournamentController::class, 'destroy'])->name('tournament.destroy');
    Route::post('/start/{tournament}',  [TournamentController::class, 'start'])->name('tournament.start');
    Route::post('/tournament/start-games/{tournament}', [TournamentController::class, 'startGames'])->name('tournament.start-games');
    Route::post('/get-game-result/{resultId}', [TournamentController::class, 'getGameResult'])->name('tournament.get-game-result');

});


Route::get('/', [TournamentController::class, 'index']);

