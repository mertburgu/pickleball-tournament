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
    Route::get('/index', [TournamentController::class, 'index'])->name('tournament.index');
    Route::get('/show/:id', [TournamentController::class, 'show'])->name('tournament.show');
    Route::get('/create', [TournamentController::class, 'create'])->name('tournament.create');
    Route::post('/create',  [TournamentController::class, 'store'])->name('tournament.store');
    Route::get('/edit/:id', [TournamentController::class, 'edit'])->name('tournament.edit');
    Route::post('/update/:id',  [TournamentController::class, 'update'])->name('tournament.update');
    Route::delete('/delete/:id',  [TournamentController::class, 'delete'])->name('tournament.destroy');
});


Route::get('/', function () {
    return view('welcome');
});
