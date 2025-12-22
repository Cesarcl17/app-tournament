<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Team;
use App\Models\TeamRequest;
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

        // Validar que el equipo no supere el tamaño máximo del torneo
        $maxPlayers = $team->tournament->team_size;
        if ($team->users()->count() >= $maxPlayers) {
            return back()->with('error', "El equipo ya tiene el máximo de {$maxPlayers} jugador(es) para este formato.");
        }

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

    /* ==========================
     *  SOLICITUDES DE UNIÓN
     * ========================== */

    /**
     * Solicitar unirse a un equipo
     */
    public function requestJoin(Request $request, Team $team)
    {
        /** @var User $user */
        $user = Auth::user();

        // Verificar si ya pertenece al equipo
        if ($team->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Ya perteneces a este equipo');
        }

        // Verificar si ya tiene una solicitud pendiente
        $existingRequest = TeamRequest::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'Ya tienes una solicitud pendiente para este equipo');
        }

        // Verificar si ya pertenece a otro equipo del mismo torneo
        if ($user->hasTeamInTournament($team->tournament)) {
            return back()->with('error', 'Ya perteneces a un equipo en este torneo');
        }

        // Crear solicitud
        TeamRequest::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'message' => $request->input('message'),
        ]);

        return back()->with('success', 'Solicitud enviada. El capitán del equipo la revisará.');
    }

    /**
     * Cancelar solicitud de unión
     */
    public function cancelRequest(Team $team)
    {
        /** @var User $user */
        $user = Auth::user();

        TeamRequest::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->delete();

        return back()->with('success', 'Solicitud cancelada');
    }

    /**
     * Ver solicitudes pendientes (solo capitán/organizador)
     */
    public function requests(Team $team)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        $pendingRequests = $team->pendingRequests()->with('user')->get();

        return view('teams.requests', compact('team', 'pendingRequests'));
    }

    /**
     * Aprobar solicitud de unión
     */
    public function approveRequest(Team $team, TeamRequest $teamRequest)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        // Verificar que la solicitud pertenece a este equipo
        if ($teamRequest->team_id !== $team->id) {
            abort(404);
        }

        // Verificar que está pendiente
        if (!$teamRequest->isPending()) {
            return back()->with('error', 'Esta solicitud ya fue procesada');
        }

        // Validar que el equipo no supere el tamaño máximo del torneo
        $maxPlayers = $team->tournament->team_size;
        if ($team->users()->count() >= $maxPlayers) {
            return back()->with('error', "El equipo ya tiene el máximo de {$maxPlayers} jugador(es) para este formato.");
        }

        // Añadir al equipo
        $team->users()->attach($teamRequest->user_id, ['role' => 'player']);

        // Actualizar estado de la solicitud
        $teamRequest->update(['status' => 'approved']);

        // Si estaba inscrito como jugador suelto, cambiar a "assigned"
        $team->tournament->users()->updateExistingPivot($teamRequest->user_id, ['status' => 'assigned']);

        return back()->with('success', "Jugador {$teamRequest->user->name} añadido al equipo");
    }

    /**
     * Rechazar solicitud de unión
     */
    public function rejectRequest(Team $team, TeamRequest $teamRequest)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        // Verificar que la solicitud pertenece a este equipo
        if ($teamRequest->team_id !== $team->id) {
            abort(404);
        }

        // Verificar que está pendiente
        if (!$teamRequest->isPending()) {
            return back()->with('error', 'Esta solicitud ya fue procesada');
        }

        // Rechazar
        $teamRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Solicitud rechazada');
    }
}
