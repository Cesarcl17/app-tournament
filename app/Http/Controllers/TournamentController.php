<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\User;
use App\Notifications\BracketGenerated;
use App\Notifications\DisputeResolved;
use App\Notifications\MatchDisputed;
use App\Notifications\MatchResultReported;
use App\Notifications\MatchScheduled;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::with('game');

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filtrar por juego si viene el parámetro
        if ($request->filled('game')) {
            $game = Game::where('slug', $request->game)->first();
            if ($game) {
                $query->where('game_id', $game->id);
            }
        }

        // Filtrar por formato (1v1, 3v3, 5v5)
        if ($request->filled('format')) {
            $query->where('team_size', $request->format);
        }

        // Filtrar por estado
        if ($request->filled('status')) {
            $today = Carbon::now()->toDateString();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('start_date', '>', $today);
                    break;
                case 'active':
                    $query->where('start_date', '<=', $today)
                          ->where('end_date', '>=', $today);
                    break;
                case 'finished':
                    $query->where('end_date', '<', $today);
                    break;
            }
        }

        $tournaments = $query->orderBy('start_date', 'desc')->paginate(12)->withQueryString();
        $games = Game::active()->orderBy('name')->get();

        // Preservar filtros actuales
        $filters = [
            'search' => $request->search,
            'game' => $request->game,
            'format' => $request->format,
            'status' => $request->status,
        ];

        return view('torneos.index', compact('tournaments', 'games', 'filters'));
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
            'rules' => 'nullable|string',
            'check_in_minutes' => 'required|integer|min:15|max:120',
            'prizes' => 'nullable|array',
            'prizes.*.name' => 'required_with:prizes|string|max:100',
            'prizes.*.description' => 'nullable|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'game_id' => 'required|exists:games,id',
            'team_size' => 'required|in:1,3,5',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
        ], [
            'start_date.after_or_equal' => 'La fecha de inicio debe ser hoy o una fecha futura.',
            'team_size.in' => 'El formato debe ser 1v1, 3v3 o 5v5.',
            'check_in_minutes.min' => 'El tiempo de check-in debe ser mínimo 15 minutos.',
            'banner.max' => 'El banner no puede superar 10MB.',
        ]);

        $data = $request->except(['prizes', 'banner']);
        $data['prizes'] = $request->input('prizes', []);

        // Manejar subida de banner
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('tournaments', 'public');
        }

        Tournament::create($data);

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
            'rules' => 'nullable|string',
            'check_in_minutes' => 'required|integer|min:15|max:120',
            'prizes' => 'nullable|array',
            'prizes.*.name' => 'required_with:prizes|string|max:100',
            'prizes.*.description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'game_id' => 'required|exists:games,id',
            'team_size' => 'required|in:1,3,5',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
        ], [
            'team_size.in' => 'El formato debe ser 1v1, 3v3 o 5v5.',
            'check_in_minutes.min' => 'El tiempo de check-in debe ser mínimo 15 minutos.',
            'banner.max' => 'El banner no puede superar 10MB.',
        ]);

        $data = $request->except(['prizes', 'banner']);
        $data['prizes'] = $request->input('prizes', []);

        // Manejar subida de banner
        if ($request->hasFile('banner')) {
            // Eliminar banner anterior si existe
            if ($tournament->banner && Storage::disk('public')->exists($tournament->banner)) {
                Storage::disk('public')->delete($tournament->banner);
            }
            $data['banner'] = $request->file('banner')->store('tournaments', 'public');
        }

        $tournament->update($data);

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

        // Eliminar banner si existe
        if ($tournament->banner && Storage::disk('public')->exists($tournament->banner)) {
            Storage::disk('public')->delete($tournament->banner);
        }

        $tournament->delete();

        return redirect()
            ->route('torneos.index')
            ->with('success', 'Torneo eliminado correctamente');
    }

    /**
     * Eliminar banner del torneo
     */
    public function deleteBanner(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para editar torneos');
        }

        if ($tournament->banner && Storage::disk('public')->exists($tournament->banner)) {
            Storage::disk('public')->delete($tournament->banner);
        }

        $tournament->update(['banner' => null]);

        return back()->with('success', 'Banner eliminado correctamente');
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

    /**
     * Mostrar el bracket del torneo (público)
     */
    public function showBracket(Tournament $tournament)
    {
        if (!$tournament->hasBracket()) {
            return redirect()->route('torneos.show', $tournament)
                ->with('error', 'Este torneo aún no tiene bracket generado');
        }

        $matchesByRound = $tournament->getMatchesByRound();
        $totalRounds = $tournament->getTotalRounds();
        $champion = $tournament->getChampion();

        return view('torneos.bracket', compact('tournament', 'matchesByRound', 'totalRounds', 'champion'));
    }

    /**
     * Generar el bracket del torneo (solo admin/organizador)
     */
    public function generateBracket(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para generar el bracket');
        }

        // Verificar que no existe bracket previo
        if ($tournament->hasBracket()) {
            return back()->with('error', 'El bracket ya ha sido generado');
        }

        // Verificar mínimo de equipos
        $teams = $tournament->teams()->get();
        if ($teams->count() < Tournament::MIN_TEAMS_FOR_BRACKET) {
            return back()->with('error', 'Se necesitan mínimo ' . Tournament::MIN_TEAMS_FOR_BRACKET . ' equipos para generar el bracket');
        }

        // Shuffle aleatorio de equipos
        $shuffledTeams = $teams->shuffle()->values();
        $numTeams = $shuffledTeams->count();

        // Calcular rondas y slots
        $totalRounds = (int) ceil(log($numTeams, 2));
        $totalSlots = (int) pow(2, $totalRounds);
        $numByes = $totalSlots - $numTeams;

        // Crear estructura de partidas para todas las rondas
        $matchesPerRound = [];
        for ($round = 1; $round <= $totalRounds; $round++) {
            $matchesPerRound[$round] = (int) ($totalSlots / pow(2, $round));
        }

        // Crear partidas vacías para todas las rondas
        $allMatches = [];
        for ($round = 1; $round <= $totalRounds; $round++) {
            for ($position = 0; $position < $matchesPerRound[$round]; $position++) {
                $allMatches[$round][$position] = TournamentMatch::create([
                    'tournament_id' => $tournament->id,
                    'round' => $round,
                    'position' => $position,
                    'status' => TournamentMatch::STATUS_PENDING,
                ]);
            }
        }

        // Asignar equipos a la primera ronda
        // Los primeros $numByes equipos reciben BYE y avanzan automáticamente
        $teamIndex = 0;
        $matchPosition = 0;

        // Distribuir equipos con y sin BYE
        for ($position = 0; $position < $matchesPerRound[1]; $position++) {
            $match = $allMatches[1][$position];

            if ($position < $numByes) {
                // Este partido tiene BYE: solo un equipo que avanza automáticamente
                $team = $shuffledTeams[$teamIndex++];
                $match->update([
                    'team1_id' => $team->id,
                    'team2_id' => null,
                    'winner_id' => $team->id,
                    'status' => TournamentMatch::STATUS_COMPLETED,
                ]);

                // Propagar ganador a siguiente ronda
                $nextMatch = $match->getNextMatch();
                if ($nextMatch) {
                    if ($position % 2 === 0) {
                        $nextMatch->update(['team1_id' => $team->id]);
                    } else {
                        $nextMatch->update(['team2_id' => $team->id]);
                    }
                }
            } else {
                // Partido normal: dos equipos
                $team1 = $shuffledTeams[$teamIndex++];
                $team2 = $shuffledTeams[$teamIndex++];
                $match->update([
                    'team1_id' => $team1->id,
                    'team2_id' => $team2->id,
                ]);
            }
        }

        // Notificar a todos los participantes
        $this->notifyBracketGenerated($tournament);

        return redirect()->route('torneos.bracket', $tournament)
            ->with('success', 'Bracket generado correctamente con ' . $numTeams . ' equipos');
    }

    /**
     * Notificar a los capitanes cuando se genera el bracket
     */
    private function notifyBracketGenerated(Tournament $tournament): void
    {
        // Obtener todos los usuarios de los equipos participantes
        $userIds = DB::table('team_user')
            ->join('tournament_user', 'team_user.team_id', '=', 'tournament_user.team_id')
            ->where('tournament_user.tournament_id', $tournament->id)
            ->pluck('team_user.user_id')
            ->unique();

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $user->notify(new BracketGenerated($tournament));
        }
    }

    /**
     * Resetear el bracket del torneo (solo admin)
     */
    public function resetBracket(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        // Solo admin puede resetear
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Solo los administradores pueden resetear el bracket');
        }

        if (!$tournament->hasBracket()) {
            return back()->with('error', 'Este torneo no tiene bracket para resetear');
        }

        $tournament->deleteBracket();

        return redirect()->route('torneos.show', $tournament)
            ->with('success', 'Bracket eliminado correctamente');
    }

    /**
     * Actualizar resultado de una partida (solo admin/organizador)
     */
    public function updateMatchResult(Request $request, Tournament $tournament, TournamentMatch $match)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para actualizar resultados');
        }

        // Verificar que la partida pertenece al torneo
        if ($match->tournament_id !== $tournament->id) {
            abort(404, 'Partida no encontrada en este torneo');
        }

        // Verificar que la partida puede ser actualizada
        if (!$match->canBeUpdated()) {
            return back()->with('error', 'Esta partida no puede ser actualizada');
        }

        $request->validate([
            'winner_id' => 'required|in:' . $match->team1_id . ',' . $match->team2_id,
            'score_team1' => 'nullable|integer|min:0|max:255',
            'score_team2' => 'nullable|integer|min:0|max:255',
        ], [
            'winner_id.required' => 'Debes seleccionar un ganador',
            'winner_id.in' => 'El ganador debe ser uno de los equipos de la partida',
        ]);

        $match->setWinner(
            (int) $request->winner_id,
            $request->score_team1,
            $request->score_team2
        );

        return back()->with('success', 'Resultado guardado correctamente');
    }

    /**
     * Programar fecha/hora de una partida (solo admin/organizador)
     */
    public function scheduleMatch(Request $request, Tournament $tournament, TournamentMatch $match)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para programar partidas');
        }

        // Verificar que la partida pertenece al torneo
        if ($match->tournament_id !== $tournament->id) {
            abort(404, 'Partida no encontrada en este torneo');
        }

        $request->validate([
            'scheduled_at' => 'required|date|after_or_equal:now',
        ], [
            'scheduled_at.required' => 'Debes indicar una fecha y hora',
            'scheduled_at.after_or_equal' => 'La fecha debe ser ahora o en el futuro',
        ]);

        $match->update([
            'scheduled_at' => $request->scheduled_at,
        ]);

        // Notificar a los jugadores de ambos equipos
        $this->notifyMatchScheduled($match);

        return back()->with('success', 'Partida programada correctamente');
    }

    /**
     * Notifica a ambos equipos que su partida fue programada
     */
    private function notifyMatchScheduled(TournamentMatch $match): void
    {
        if (!$match->team1 || !$match->team2) {
            return;
        }

        $allUsers = $match->team1->users->merge($match->team2->users);

        foreach ($allUsers as $user) {
            $user->notify(new MatchScheduled($match));
        }
    }

    /**
     * Reportar resultado por capitán
     */
    public function reportMatchResult(Request $request, Tournament $tournament, TournamentMatch $match)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar que la partida pertenece al torneo
        if ($match->tournament_id !== $tournament->id) {
            abort(404, 'Partida no encontrada en este torneo');
        }

        // Verificar que el usuario puede reportar
        if (!$match->canUserReport($user)) {
            return back()->with('error', 'No tienes permiso para reportar el resultado de esta partida');
        }

        $request->validate([
            'score_team1' => 'required|integer|min:0|max:255',
            'score_team2' => 'required|integer|min:0|max:255',
        ], [
            'score_team1.required' => 'Debes indicar el marcador del equipo 1',
            'score_team2.required' => 'Debes indicar el marcador del equipo 2',
        ]);

        // Los marcadores no pueden ser iguales (debe haber un ganador)
        if ($request->score_team1 === $request->score_team2) {
            return back()->with('error', 'Debe haber un ganador. Los marcadores no pueden ser iguales.');
        }

        $result = $match->reportResult(
            $user,
            (int) $request->score_team1,
            (int) $request->score_team2
        );

        if ($result['success']) {
            // Si solo un equipo ha reportado, notificar al otro capitán
            if ($match->result_status === TournamentMatch::RESULT_TEAM1_REPORTED) {
                $this->notifyOtherCaptain($match, $match->team2, $match->team1->name);
            } elseif ($match->result_status === TournamentMatch::RESULT_TEAM2_REPORTED) {
                $this->notifyOtherCaptain($match, $match->team1, $match->team2->name);
            }

            return back()->with('success', $result['message']);
        } else {
            // Si hay disputa, notificar a los admins
            if (isset($result['disputed']) && $result['disputed']) {
                $this->notifyAdminsOfDispute($match);
            }

            $messageType = isset($result['disputed']) ? 'warning' : 'error';
            return back()->with($messageType, $result['message']);
        }
    }

    /**
     * Notifica al capitán del otro equipo que debe reportar
     */
    private function notifyOtherCaptain(TournamentMatch $match, $team, string $reporterTeamName): void
    {
        // Obtener el capitán del equipo
        $captain = $team->users()->wherePivot('role', 'captain')->first();

        if ($captain) {
            $captain->notify(new MatchResultReported($match, $reporterTeamName));
        }
    }

    /**
     * Notifica a todos los administradores de una disputa
     */
    private function notifyAdminsOfDispute(TournamentMatch $match): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new MatchDisputed($match));
        }
    }

    /**
     * Resolver disputa de partida (solo admin)
     */
    public function resolveDispute(Request $request, Tournament $tournament, TournamentMatch $match)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            abort(403, 'Solo los administradores pueden resolver disputas');
        }

        // Verificar que la partida pertenece al torneo
        if ($match->tournament_id !== $tournament->id) {
            abort(404, 'Partida no encontrada en este torneo');
        }

        // Verificar que está en disputa
        if (!$match->isDisputed()) {
            return back()->with('error', 'Esta partida no está en disputa');
        }

        $request->validate([
            'winner_id' => 'required|in:' . $match->team1_id . ',' . $match->team2_id,
            'score_team1' => 'required|integer|min:0|max:255',
            'score_team2' => 'required|integer|min:0|max:255',
        ]);

        $winner = $match->team1_id == $request->winner_id ? $match->team1 : $match->team2;

        $match->resolveDispute(
            (int) $request->winner_id,
            (int) $request->score_team1,
            (int) $request->score_team2
        );

        // Notificar a ambos equipos que la disputa fue resuelta
        $this->notifyDisputeResolved($match, $winner);

        return back()->with('success', 'Disputa resuelta correctamente');
    }

    /**
     * Notifica a los capitanes que la disputa fue resuelta
     */
    private function notifyDisputeResolved(TournamentMatch $match, $winner): void
    {
        // Obtener capitanes de ambos equipos
        $captain1 = $match->team1->users()->wherePivot('role', 'captain')->first();
        $captain2 = $match->team2->users()->wherePivot('role', 'captain')->first();

        if ($captain1) {
            $captain1->notify(new DisputeResolved($match, $winner));
        }

        if ($captain2) {
            $captain2->notify(new DisputeResolved($match, $winner));
        }
    }

    /**
     * Ver partidas en disputa (solo admin)
     */
    public function disputes(Tournament $tournament)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canManageTournaments()) {
            abort(403, 'No tienes permiso para ver las disputas');
        }

        $disputes = $tournament->matches()
            ->disputed()
            ->with(['team1', 'team2', 'captainReporter1', 'captainReporter2'])
            ->orderBy('round')
            ->orderBy('position')
            ->get();

        return view('torneos.disputes', compact('tournament', 'disputes'));
    }

    /**
     * Check-in de un equipo en una partida
     */
    public function checkIn(Tournament $tournament, TournamentMatch $match)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Debes iniciar sesión para hacer check-in');
        }

        // Verificar que la partida pertenece al torneo
        if ($match->tournament_id !== $tournament->id) {
            abort(404, 'Partida no encontrada en este torneo');
        }

        // Verificar que el torneo tiene check-in habilitado
        if (!$tournament->check_in_minutes || $tournament->check_in_minutes < 15) {
            return back()->with('error', 'El check-in no está habilitado en este torneo');
        }

        // Verificar que ambos equipos existen
        if (!$match->team1 || !$match->team2) {
            return back()->with('error', 'Esta partida aún no tiene equipos asignados');
        }

        // Determinar si el usuario es capitán de alguno de los equipos
        $team = null;

        $isTeam1Captain = $match->team1->users()
            ->wherePivot('role', 'captain')
            ->where('users.id', $user->id)
            ->exists();

        $isTeam2Captain = $match->team2->users()
            ->wherePivot('role', 'captain')
            ->where('users.id', $user->id)
            ->exists();

        if ($isTeam1Captain) {
            $team = $match->team1;
        } elseif ($isTeam2Captain) {
            $team = $match->team2;
        }

        if (!$team) {
            return back()->with('error', 'Solo los capitanes de los equipos pueden hacer check-in');
        }

        // Intentar hacer check-in
        $result = $match->checkIn($team);

        if ($result === false) {
            // Determinar razón del fallo
            if ($match->isCheckInExpired()) {
                return back()->with('error', 'El período de check-in ha expirado');
            } elseif (!$match->isCheckInOpen()) {
                return back()->with('error', 'El check-in aún no está disponible');
            } else {
                return back()->with('error', 'Tu equipo ya ha hecho check-in');
            }
        }

        return back()->with('success', '¡Check-in realizado correctamente para ' . $team->name . '!');
    }
}
