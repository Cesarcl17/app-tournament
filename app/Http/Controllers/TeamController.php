<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Team;
use App\Models\TeamRequest;
use App\Models\User;
use App\Notifications\TeamRequestApproved;
use App\Notifications\TeamRequestReceived;
use App\Notifications\TeamRequestRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Manejar subida de logo
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('teams', 'public');
        }

        $team = $tournament->teams()->create($data);

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
        ]);

        $data = $request->only('name', 'description');

        // Manejar subida de logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($team->logo && Storage::disk('public')->exists($team->logo)) {
                Storage::disk('public')->delete($team->logo);
            }
            $data['logo'] = $request->file('logo')->store('teams', 'public');
        }

        $team->update($data);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Equipo actualizado correctamente');
    }

    /**
     * Eliminar logo del equipo
     */
    public function deleteLogo(Team $team)
    {
        if (!$this->isCaptain($team)) {
            abort(403);
        }

        if ($team->logo && Storage::disk('public')->exists($team->logo)) {
            Storage::disk('public')->delete($team->logo);
        }

        $team->update(['logo' => null]);

        return back()->with('success', 'Logo eliminado correctamente');
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

        // Notificar al capitán del equipo
        $captain = $team->users()->wherePivot('role', 'captain')->first();
        if ($captain) {
            $captain->notify(new TeamRequestReceived($team, $user));
        }

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

        // Notificar al jugador que fue aceptado
        $teamRequest->user->notify(new TeamRequestApproved($team));

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

        // Notificar al jugador que fue rechazado
        $teamRequest->user->notify(new TeamRequestRejected($team));

        return back()->with('success', 'Solicitud rechazada');
    }

    /* ==========================
     *  ROLES DE POSICIÓN
     * ========================== */

    /**
     * Actualizar el rol/posición de un jugador en el equipo
     */
    public function updatePlayerRoles(Request $request, Team $team)
    {
        if (!Auth::check()) {
            abort(403);
        }

        // Determinar qué usuario se está actualizando
        $targetUserId = $request->input('user_id', Auth::id());
        $currentUserId = Auth::id();

        // Verificar permisos: puede editar si es el propio jugador O es capitán/admin
        $isCaptainOrAdmin = $this->isCaptain($team);
        $isSelf = $targetUserId == $currentUserId;

        if (!$isSelf && !$isCaptainOrAdmin) {
            abort(403, 'No tienes permisos para editar los roles de este jugador');
        }

        // Verificar que el jugador objetivo pertenece al equipo
        $membership = $team->users()->where('users.id', $targetUserId)->first();
        if (!$membership) {
            abort(403, 'El jugador no pertenece a este equipo');
        }

        // Obtener posiciones válidas del juego
        $game = $team->tournament->game;
        $validPositions = $game->positions ?? [];

        $request->validate([
            'primary_role' => ['nullable', 'string', function ($attribute, $value, $fail) use ($validPositions) {
                if ($value && !in_array($value, $validPositions)) {
                    $fail('La posición seleccionada no es válida para este juego.');
                }
            }],
            'secondary_role' => ['nullable', 'string', function ($attribute, $value, $fail) use ($validPositions) {
                if ($value && !in_array($value, $validPositions)) {
                    $fail('La posición seleccionada no es válida para este juego.');
                }
            }],
        ]);

        // Verificar que no sean iguales
        if ($request->primary_role && $request->primary_role === $request->secondary_role) {
            return back()->with('error', 'El rol principal y secundario no pueden ser iguales');
        }

        // Actualizar roles en la tabla pivot
        $team->users()->updateExistingPivot($targetUserId, [
            'primary_role' => $request->primary_role,
            'secondary_role' => $request->secondary_role,
        ]);

        $playerName = $isSelf ? 'Tus roles han sido actualizados' : 'Roles del jugador actualizados';
        return back()->with('success', $playerName);
    }
}
