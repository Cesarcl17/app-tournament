<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// ============================================
// RUTAS PÚBLICAS (sin login)
// ============================================
Route::get('/torneos', [TournamentController::class, 'index'])->name('torneos.index');
Route::get('/torneos/crear', [TournamentController::class, 'create'])->name('torneos.create')->middleware('auth');
Route::get('/torneos/{tournament}', [TournamentController::class, 'show'])->name('torneos.show');
Route::get('/equipos/{team}', [TeamController::class, 'show'])->name('teams.show');

// ============================================
// RUTAS PROTEGIDAS (requieren login)
// ============================================
Route::middleware('auth')->group(function () {

    // --- Perfil de usuario ---
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    // --- Gestión de Torneos (solo admin/organizer) ---
    Route::post('/torneos', [TournamentController::class, 'store'])->name('torneos.store');
    Route::get('/torneos/{tournament}/editar', [TournamentController::class, 'edit'])->name('torneos.edit');
    Route::put('/torneos/{tournament}', [TournamentController::class, 'update'])->name('torneos.update');
    Route::delete('/torneos/{tournament}', [TournamentController::class, 'destroy'])->name('torneos.destroy');

    // --- Inscripción a Torneos ---
    Route::post('/torneos/{tournament}/inscribirse', [TournamentController::class, 'register'])->name('torneos.register');
    Route::delete('/torneos/{tournament}/cancelar-inscripcion', [TournamentController::class, 'unregister'])->name('torneos.unregister');

    // --- Gestión de Jugadores Inscritos (solo admin/organizer) ---
    Route::get('/torneos/{tournament}/jugadores', [TournamentController::class, 'players'])->name('torneos.players');
    Route::post('/torneos/{tournament}/asignar-jugador', [TournamentController::class, 'assignPlayer'])->name('torneos.assignPlayer');

    // --- Gestión de Equipos ---
    Route::get('/torneos/{tournament}/equipos/crear', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/torneos/{tournament}/equipos', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/equipos/{team}/editar', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/equipos/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/equipos/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

    // --- Gestión de Jugadores en Equipos ---
    Route::post('/equipos/{team}/users', [TeamController::class, 'addUser'])->name('teams.users.add');
    Route::post('/equipos/{team}/users/{user}/captain', [TeamController::class, 'makeCaptain'])->name('teams.makeCaptain');
    Route::delete('/equipos/{team}/users/{user}', [TeamController::class, 'removeUser'])->name('teams.users.remove');

    // --- Solicitudes de unión a Equipos ---
    Route::post('/equipos/{team}/solicitar', [TeamController::class, 'requestJoin'])->name('teams.requestJoin');
    Route::delete('/equipos/{team}/cancelar-solicitud', [TeamController::class, 'cancelRequest'])->name('teams.cancelRequest');
    Route::get('/equipos/{team}/solicitudes', [TeamController::class, 'requests'])->name('teams.requests');
    Route::post('/equipos/{team}/solicitudes/{teamRequest}/aprobar', [TeamController::class, 'approveRequest'])->name('teams.approveRequest');
    Route::post('/equipos/{team}/solicitudes/{teamRequest}/rechazar', [TeamController::class, 'rejectRequest'])->name('teams.rejectRequest');
});

// ============================================
// AUTENTICACIÓN
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


