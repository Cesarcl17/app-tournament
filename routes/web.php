<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('inicio');
});

//Rutas torneos y equipos

Route::get('/torneos', [TournamentController::class, 'index'])->name('torneos.index');

Route::get('/torneos/crear', [TournamentController::class, 'create'])->name('torneos.create');
Route::post('/torneos', [TournamentController::class, 'store'])->name('torneos.store');
Route::get('/torneos/{tournament}', [TournamentController::class, 'show'])
    ->name('torneos.show');
Route::get('/torneos/{tournament}/editar', [TournamentController::class, 'edit'])
    ->name('torneos.edit');

Route::put('/torneos/{tournament}', [TournamentController::class, 'update'])
    ->name('torneos.update');
Route::delete('/torneos/{tournament}', [TournamentController::class, 'destroy'])
    ->name('torneos.destroy');

Route::prefix('torneos/{tournament}')->group(function () {
Route::get('equipos/crear', [TeamController::class, 'create'])
    ->name('teams.create');

Route::post('equipos', [TeamController::class, 'store'])
    ->name('teams.store');
});

Route::get('equipos/{team}/editar', [TeamController::class, 'edit'])->name('teams.edit');
Route::put('equipos/{team}', [TeamController::class, 'update'])->name('teams.update');
Route::delete('equipos/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

Route::get('equipos/{team}', [TeamController::class, 'show'])
    ->name('teams.show');


Route::post('/equipos/{team}/users', [TeamController::class, 'addUser'])
    ->name('teams.users.add');

Route::post(
    '/equipos/{team}/capitan/{user}',
    [TeamController::class, 'makeCaptain']
)->name('teams.makeCaptain');

Route::delete(
    '/equipos/{team}/users/{user}',
    [TeamController::class, 'removeUser']
)->name('teams.users.remove');

Route::get('/equipos/{team}', [TeamController::class, 'show']);




//---------------------------

Route::get('equipos/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::post('equipos/{team}/users', [TeamController::class, 'addUser'])->name('teams.users.add');
Route::post('equipos/{team}/users/{user}/captain', [TeamController::class, 'makeCaptain'])->name('teams.makeCaptain');
Route::delete('equipos/{team}/users/{user}', [TeamController::class, 'removeUser'])->name('teams.users.remove');


//------------LOGIN TEMPORAL
// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


