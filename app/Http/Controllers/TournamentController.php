<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::with('game');

        // Filtrar por juego si viene el parámetro
        if ($request->has('game') && $request->game) {
            $game = Game::where('slug', $request->game)->first();
            if ($game) {
                $query->where('game_id', $game->id);
            }
        }

        $tournaments = $query->orderBy('start_date', 'desc')->get();
        $games = Game::active()->orderBy('name')->get();
        $currentGame = $request->game;

        return view('torneos.index', compact('tournaments', 'games', 'currentGame'));
    }

    public function create(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para crear torneos');
        }

        $games = Game::active()->orderBy('name')->get();

        // Preseleccionar juego si viene por parámetro
        $selectedGame = null;
        if ($request->has('game')) {
            $selectedGame = Game::where('slug', $request->game)->first();
        }

        return view('torneos.create', compact('games', 'selectedGame'));
    }

    public function store(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para crear torneos');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'game_id' => 'required|exists:games,id',
            'team_size' => 'required|in:1,3,5',
        ], [
            'start_date.after_or_equal' => 'La fecha de inicio debe ser hoy o una fecha futura.',
            'team_size.in' => 'El formato debe ser 1v1, 3v3 o 5v5.',
        ]);

        Tournament::create($request->all());

        return redirect()
            ->route('torneos.index')
            ->with('success', 'Torneo creado correctamente');
    }

    public function show(Tournament $tournament)
    {
        $teams = $tournament->teams;
        return view('torneos.show', compact('tournament', 'teams'));
    }

    public function edit(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para editar torneos');
        }

        $games = Game::active()->orderBy('name')->get();

        return view('torneos.edit', compact('tournament', 'games'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para editar torneos');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'game_id' => 'required|exists:games,id',
            'team_size' => 'required|in:1,3,5',
        ], [
            'team_size.in' => 'El formato debe ser 1v1, 3v3 o 5v5.',
        ]);

        $tournament->update($request->all());

        return redirect()
            ->route('torneos.show', $tournament)
            ->with('success', 'Torneo actualizado correctamente');
    }

    public function destroy(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para eliminar torneos');
        }

        $tournament->delete();

        return redirect()
            ->route('torneos.index')
            ->with('success', 'Torneo eliminado correctamente');
    }

    /**
     * Inscribirse a un torneo como jugador suelto
     */
    public function register(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para inscribirte');
        }

        // Verificar si ya está inscrito
        if ($user->isRegisteredInTournament($tournament)) {
            return back()->with('error', 'Ya estás inscrito en este torneo');
        }

        // Verificar si ya tiene equipo en el torneo
        if ($user->hasTeamInTournament($tournament)) {
            return back()->with('error', 'Ya perteneces a un equipo en este torneo');
        }

        // Inscribir al usuario
        $tournament->users()->attach($user->id, ['status' => 'registered']);

        return back()->with('success', 'Te has inscrito correctamente al torneo');
    }

    /**
     * Cancelar inscripción a un torneo
     */
    public function unregister(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Solo puede cancelar si está como "registered" (sin equipo)
        $tournament->users()->wherePivot('status', 'registered')->detach($user->id);

        return back()->with('success', 'Has cancelado tu inscripción al torneo');
    }

    /**
     * Ver jugadores inscritos sin equipo (solo organizador/admin)
     */
    public function players(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para ver esta página');
        }

        $registeredUsers = $tournament->registeredUsers;
        $teams = $tournament->teams;

        return view('torneos.players', compact('tournament', 'registeredUsers', 'teams'));
    }

    /**
     * Asignar jugador a un equipo (solo organizador/admin)
     */
    public function assignPlayer(Request $request, Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para asignar jugadores');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $playerToAssign = User::findOrFail($request->user_id);
        $team = \App\Models\Team::findOrFail($request->team_id);

        // Verificar que el equipo pertenece al torneo
        if ($team->tournament_id !== $tournament->id) {
            return back()->with('error', 'El equipo no pertenece a este torneo');
        }

        // Añadir al equipo
        $team->users()->attach($playerToAssign->id, ['role' => 'player']);

        // Cambiar estado a "assigned"
        $tournament->users()->updateExistingPivot($playerToAssign->id, ['status' => 'assigned']);

        return back()->with('success', "Jugador {$playerToAssign->name} asignado al equipo {$team->name}");
    }
}
