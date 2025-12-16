<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /* ==========================
     *  PERMISOS
     * ========================== */

    private function isCaptain(Team $team): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Admin puede hacer todo
        if (Auth::user()->role === 'admin') {
            return true;
        }

        return $team->users()
            ->where('users.id', Auth::id())
            ->wherePivot('role', 'captain')
            ->exists();
    }

    /* ==========================
     *  CREAR EQUIPO
     * ========================== */

    public function create(Tournament $tournament)
    {
        return view('teams.create', compact('tournament'));
    }

    public function store(Request $request, Tournament $tournament)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team = $tournament->teams()->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // El creador pasa a ser capitán
        $team->users()->attach(Auth::id(), ['role' => 'captain']);

        return redirect()
            ->route('torneos.show', $tournament)
            ->with('success', 'Equipo creado correctamente');
    }

    /* ==========================
     *  VER EQUIPO
     * ========================== */

    public function show(Team $team)
    {
        $team->load('users');

        $isCaptain = $this->isCaptain($team);

        $availableUsers = User::whereNotIn(
            'id',
            $team->users->pluck('id')
        )->get();

        return view('teams.show', compact(
            'team',
            'isCaptain',
            'availableUsers'
        ));
    }

    /* ==========================
     *  EDITAR EQUIPO
     * ========================== */

    public function edit(Team $team)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        return view('teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team->update($request->only('name', 'description'));

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Equipo actualizado correctamente');
    }

    /* ==========================
     *  BORRAR EQUIPO
     * ========================== */

    public function destroy(Team $team)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        $tournamentId = $team->tournament_id;
        $team->delete();

        return redirect()
            ->route('torneos.show', $tournamentId)
            ->with('success', 'Equipo eliminado correctamente');
    }

    /* ==========================
     *  JUGADORES
     * ========================== */

    public function addUser(Request $request, Team $team)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if (!$team->users()->where('user_id', $request->user_id)->exists()) {
            $team->users()->attach($request->user_id, ['role' => 'player']);
        }

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Jugador añadido al equipo');
    }

    public function removeUser(Team $team, User $user)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        $team->users()->detach($user->id);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Jugador eliminado del equipo');
    }

    public function makeCaptain(Team $team, User $user)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        $team->users()->updateExistingPivot(
            $team->users()
                ->wherePivot('role', 'captain')
                ->pluck('users.id'),
            ['role' => 'player']
        );

        $team->users()->updateExistingPivot(
            $user->id,
            ['role' => 'captain']
        );

        return back()->with('success', 'Capitán asignado correctamente');
    }
}
