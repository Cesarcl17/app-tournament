<?php

use App\Http\Controllers\TournamentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HeadToHeadController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// ============================================
// RUTAS DE ADMINISTRACIÓN
// ============================================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // --- Gestión de Juegos (CRUD) ---
    Route::get('/admin/games', [GameController::class, 'index'])->name('admin.games.index');
    Route::get('/admin/games/create', [GameController::class, 'create'])->name('admin.games.create');
    Route::post('/admin/games', [GameController::class, 'store'])->name('admin.games.store');
    Route::get('/admin/games/{game}/edit', [GameController::class, 'edit'])->name('admin.games.edit');
    Route::put('/admin/games/{game}', [GameController::class, 'update'])->name('admin.games.update');
    Route::delete('/admin/games/{game}', [GameController::class, 'destroy'])->name('admin.games.destroy');
    Route::delete('/admin/games/{game}/logo', [GameController::class, 'deleteLogo'])->name('admin.games.delete-logo');
});

// ============================================
// RUTAS PÚBLICAS (sin login)
// ============================================
Route::get('/torneos', [TournamentController::class, 'index'])->name('torneos.index');
Route::get('/torneos/crear', [TournamentController::class, 'create'])->name('torneos.create')->middleware('auth');
Route::get('/torneos/{tournament}', [TournamentController::class, 'show'])->name('torneos.show');
Route::get('/equipos/{team}', [TeamController::class, 'show'])->name('teams.show');

// --- Perfiles de Usuario ---
Route::get('/usuarios/{user}', [UserController::class, 'show'])->name('users.show');

// --- Head to Head ---
Route::get('/equipos/{team}/rivales', [HeadToHeadController::class, 'rivals'])->name('head-to-head.rivals');
Route::get('/head-to-head/{team1}/{team2}', [HeadToHeadController::class, 'show'])->name('head-to-head.show');

// --- Activity Feed ---
Route::get('/actividad', [App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');
Route::get('/api/actividad', [App\Http\Controllers\ActivityController::class, 'recent'])->name('activities.recent');

// --- Rankings ---
Route::get('/rankings', [App\Http\Controllers\RankingController::class, 'index'])->name('rankings.index');

// --- Calendario ---
Route::get('/calendario', [CalendarController::class, 'index'])->name('calendario.index');
Route::get('/calendario/partidas', [CalendarController::class, 'matches'])->name('calendario.matches');

// --- Comentarios de partidas (vista pública) ---
Route::get('/partidas/{match}/comentarios', [App\Http\Controllers\MatchCommentController::class, 'index'])->name('matches.comments.index');

// ============================================
// RUTAS PROTEGIDAS (requieren login)
// ============================================
Route::middleware('auth')->group(function () {

    // --- Comentarios de partidas ---
    Route::post('/partidas/{match}/comentarios', [App\Http\Controllers\MatchCommentController::class, 'store'])->name('matches.comments.store');
    Route::delete('/comentarios/{comment}', [App\Http\Controllers\MatchCommentController::class, 'destroy'])->name('matches.comments.destroy');

    // --- Notificaciones ---
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notificaciones/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notificaciones/{id}/leer', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('/notificaciones/leer-todas', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notificaciones/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notificaciones', [NotificationController::class, 'destroyRead'])->name('notifications.destroyRead');

    // --- Perfil de usuario ---
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    // --- Gestión de Torneos (solo admin/organizer) ---
    Route::post('/torneos', [TournamentController::class, 'store'])->name('torneos.store');
    Route::get('/torneos/{tournament}/editar', [TournamentController::class, 'edit'])->name('torneos.edit');
    Route::put('/torneos/{tournament}', [TournamentController::class, 'update'])->name('torneos.update');
    Route::delete('/torneos/{tournament}', [TournamentController::class, 'destroy'])->name('torneos.destroy');
    Route::delete('/torneos/{tournament}/banner', [TournamentController::class, 'deleteBanner'])->name('torneos.delete-banner');

    // --- Inscripción a Torneos ---
    Route::post('/torneos/{tournament}/inscribirse', [TournamentController::class, 'register'])->name('torneos.register');
    Route::delete('/torneos/{tournament}/cancelar-inscripcion', [TournamentController::class, 'unregister'])->name('torneos.unregister');

    // --- Gestión de Jugadores Inscritos (solo admin/organizer) ---
    Route::get('/torneos/{tournament}/jugadores', [TournamentController::class, 'players'])->name('torneos.players');
    Route::post('/torneos/{tournament}/asignar-jugador', [TournamentController::class, 'assignPlayer'])->name('torneos.assignPlayer');

    // --- Bracket del Torneo ---
    Route::get('/torneos/{tournament}/bracket', [TournamentController::class, 'showBracket'])->name('torneos.bracket');
    Route::post('/torneos/{tournament}/bracket/generar', [TournamentController::class, 'generateBracket'])->name('torneos.generateBracket');
    Route::delete('/torneos/{tournament}/bracket', [TournamentController::class, 'resetBracket'])->name('torneos.resetBracket');
    Route::put('/torneos/{tournament}/partidas/{match}', [TournamentController::class, 'updateMatchResult'])->name('torneos.updateMatch');
    Route::patch('/torneos/{tournament}/partidas/{match}/programar', [TournamentController::class, 'scheduleMatch'])->name('torneos.scheduleMatch');

    // --- Resultados por Capitanes ---
    Route::post('/torneos/{tournament}/partidas/{match}/reportar', [TournamentController::class, 'reportMatchResult'])->name('torneos.reportMatch');
    Route::post('/torneos/{tournament}/partidas/{match}/resolver-disputa', [TournamentController::class, 'resolveDispute'])->name('torneos.resolveDispute');
    Route::get('/torneos/{tournament}/disputas', [TournamentController::class, 'disputes'])->name('torneos.disputes');

    // --- Check-in de Partidas ---
    Route::post('/torneos/{tournament}/partidas/{match}/checkin', [TournamentController::class, 'checkIn'])->name('torneos.checkIn');

    // --- Gestión de Equipos ---
    Route::get('/torneos/{tournament}/equipos/crear', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/torneos/{tournament}/equipos', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/equipos/{team}/editar', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/equipos/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/equipos/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::delete('/equipos/{team}/logo', [TeamController::class, 'deleteLogo'])->name('teams.delete-logo');

    // --- Gestión de Jugadores en Equipos ---
    Route::post('/equipos/{team}/users', [TeamController::class, 'addUser'])->name('teams.users.add');
    Route::post('/equipos/{team}/users/{user}/captain', [TeamController::class, 'makeCaptain'])->name('teams.makeCaptain');
    Route::delete('/equipos/{team}/users/{user}', [TeamController::class, 'removeUser'])->name('teams.users.remove');
    Route::post('/equipos/{team}/roles', [TeamController::class, 'updatePlayerRoles'])->name('teams.updateRoles');

    // --- Solicitudes de unión a Equipos ---
    Route::post('/equipos/{team}/solicitar', [TeamController::class, 'requestJoin'])->name('teams.requestJoin');
    Route::delete('/equipos/{team}/cancelar-solicitud', [TeamController::class, 'cancelRequest'])->name('teams.cancelRequest');
    Route::get('/equipos/{team}/solicitudes', [TeamController::class, 'requests'])->name('teams.requests');
    Route::post('/equipos/{team}/solicitudes/{teamRequest}/aprobar', [TeamController::class, 'approveRequest'])->name('teams.approveRequest');
    Route::post('/equipos/{team}/solicitudes/{teamRequest}/rechazar', [TeamController::class, 'rejectRequest'])->name('teams.rejectRequest');

    // --- Invitaciones a Equipos ---
    Route::get('/equipos/{team}/invitar', [App\Http\Controllers\InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/equipos/{team}/invitar', [App\Http\Controllers\InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('/invitaciones/{invitation}', [App\Http\Controllers\InvitationController::class, 'destroy'])->name('invitations.destroy');
});

// --- Invitaciones (rutas públicas) ---
Route::get('/invitacion/{token}', [App\Http\Controllers\InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitacion/{token}/aceptar', [App\Http\Controllers\InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invitacion/{token}/rechazar', [App\Http\Controllers\InvitationController::class, 'reject'])->name('invitations.reject');

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


