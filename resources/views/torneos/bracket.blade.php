@extends('layouts.app')

@section('title', 'Bracket - ' . $tournament->name)

@section('content')
    <div class="page-header">
        <h1>🏆 Bracket: {{ $tournament->name }}</h1>
        <div class="actions-inline">
            <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-secondary">Volver al torneo</a>
            @auth
                @if(auth()->user()->canManageTournaments())
                    @php
                        $disputedCount = $tournament->matches()->where('result_status', 'disputed')->count();
                    @endphp
                    @if($disputedCount > 0)
                        <a href="{{ route('torneos.disputes', $tournament) }}" class="btn btn-danger">
                            ⚠️ Disputas ({{ $disputedCount }})
                        </a>
                    @endif
                @endif
                @if(auth()->user()->role === 'admin' && $tournament->hasBracket())
                    <form action="{{ route('torneos.resetBracket', $tournament) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar el bracket completo? Esta acción no se puede deshacer.')">
                            Resetear Bracket
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>

    @if($champion)
        <div class="champion-banner">
            <span class="champion-icon">🏆</span>
            <div class="champion-info">
                <span class="champion-label">Campeón del Torneo</span>
                <span class="champion-name">{{ $champion->name }}</span>
            </div>
        </div>
    @endif

    <div class="bracket-container">
        @for($round = 1; $round <= $totalRounds; $round++)
            <div class="bracket-round" data-round="{{ $round }}">
                <div class="bracket-round-title">
                    @php
                        $roundsFromEnd = $totalRounds - $round + 1;
                        $roundName = match ($roundsFromEnd) {
                            1 => 'Final',
                            2 => 'Semifinales',
                            3 => 'Cuartos',
                            4 => 'Octavos',
                            default => "Ronda $round",
                        };
                    @endphp
                    {{ $roundName }}
                </div>

                <div class="bracket-matches">
                    @if(isset($matchesByRound[$round]))
                        @foreach($matchesByRound[$round] as $match)
                            @php
                                $user = auth()->user();
                                $isCaptainTeam1 = $user && $match->team1 && $match->isCaptainOfTeam1($user);
                                $isCaptainTeam2 = $user && $match->team2 && $match->isCaptainOfTeam2($user);
                                $canReport = $user && $match->canUserReport($user);
                                $hasReported = ($isCaptainTeam1 && $match->hasTeam1Reported()) || ($isCaptainTeam2 && $match->hasTeam2Reported());
                            @endphp
                            <div class="bracket-match {{ $match->isCompleted() ? 'match-completed' : 'match-pending' }} {{ $match->isBye() ? 'match-bye' : '' }} {{ $match->isDisputed() ? 'match-disputed' : '' }}">
                                {{-- Estado del resultado --}}
                                @if($match->hasTeams() && !$match->isBye() && !$match->isCompleted())
                                    <div class="match-status-bar">
                                        <span class="badge {{ $match->isDisputed() ? 'badge-danger' : 'badge-warning' }}">
                                            {{ $match->getResultStatusLabel() }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Equipo 1 --}}
                                <div class="bracket-team {{ $match->winner_id === $match->team1_id ? 'team-winner' : '' }} {{ $match->isCompleted() && $match->winner_id !== $match->team1_id ? 'team-loser' : '' }}">
                                    <span class="team-name">
                                        @if($match->team1)
                                            {{ $match->team1->name }}
                                            @if($isCaptainTeam1)
                                                <span class="badge badge-primary">Tu equipo</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Por definir</span>
                                        @endif
                                    </span>
                                    @if($match->isCompleted() && $match->score_team1 !== null)
                                        <span class="team-score">{{ $match->score_team1 }}</span>
                                    @endif
                                </div>

                                <div class="bracket-vs">VS</div>

                                {{-- Equipo 2 --}}
                                <div class="bracket-team {{ $match->winner_id === $match->team2_id ? 'team-winner' : '' }} {{ $match->isCompleted() && $match->winner_id !== $match->team2_id ? 'team-loser' : '' }}">
                                    <span class="team-name">
                                        @if($match->team2)
                                            {{ $match->team2->name }}
                                            @if($isCaptainTeam2)
                                                <span class="badge badge-primary">Tu equipo</span>
                                            @endif
                                        @elseif($match->isBye())
                                            <span class="text-muted">BYE</span>
                                        @else
                                            <span class="text-muted">Por definir</span>
                                        @endif
                                    </span>
                                    @if($match->isCompleted() && $match->score_team2 !== null)
                                        <span class="team-score">{{ $match->score_team2 }}</span>
                                    @endif
                                </div>

                                {{-- Fecha programada --}}
                                @if($match->scheduled_at)
                                    <div class="bracket-schedule">
                                        📅 {{ $match->scheduled_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif

                                {{-- Formulario para CAPITANES reportar resultado --}}
                                @auth
                                    @if($canReport && !$hasReported && !$match->isDisputed())
                                        <div class="bracket-actions-match captain-report">
                                            <p class="text-muted" style="font-size: 0.85rem; margin-bottom: 8px;">
                                                📝 Reporta el resultado de tu partida:
                                            </p>
                                            <form action="{{ route('torneos.reportMatch', [$tournament, $match]) }}" method="POST" class="match-result-form">
                                                @csrf
                                                <div class="result-inputs">
                                                    <div class="result-team">
                                                        <small>{{ $match->team1->name }}</small>
                                                        <input type="number" name="score_team1" class="score-input" placeholder="0" min="0" max="255" required>
                                                    </div>
                                                    <span>-</span>
                                                    <div class="result-team">
                                                        <small>{{ $match->team2->name }}</small>
                                                        <input type="number" name="score_team2" class="score-input" placeholder="0" min="0" max="255" required>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-success">Enviar Resultado</button>
                                            </form>
                                        </div>
                                    @elseif($hasReported && !$match->isCompleted())
                                        <div class="bracket-actions-match">
                                            <span class="badge badge-success">✓ Ya reportaste tu resultado</span>
                                            <p class="text-muted" style="font-size: 0.8rem; margin-top: 5px;">
                                                Esperando al otro capitán...
                                            </p>
                                        </div>
                                    @endif
                                @endauth

                                {{-- Acciones para admin/organizador --}}
                                @auth
                                    @if(auth()->user()->canManageTournaments())
                                        <div class="bracket-actions-match admin-actions">
                                            @if($match->isDisputed())
                                                {{-- Mostrar reportes en conflicto --}}
                                                <div class="dispute-info">
                                                    <p><strong>Reporte Equipo 1:</strong> {{ $match->score_team1_by_captain1 }} - {{ $match->score_team2_by_captain1 }}</p>
                                                    <p><strong>Reporte Equipo 2:</strong> {{ $match->score_team1_by_captain2 }} - {{ $match->score_team2_by_captain2 }}</p>
                                                </div>
                                                <form action="{{ route('torneos.resolveDispute', [$tournament, $match]) }}" method="POST" class="match-result-form">
                                                    @csrf
                                                    <div class="result-inputs">
                                                        <input type="number" name="score_team1" class="score-input" placeholder="0" min="0" max="255" required>
                                                        <span>-</span>
                                                        <input type="number" name="score_team2" class="score-input" placeholder="0" min="0" max="255" required>
                                                    </div>
                                                    <div class="winner-select">
                                                        <label>Ganador:</label>
                                                        <select name="winner_id" required>
                                                            <option value="">Seleccionar...</option>
                                                            <option value="{{ $match->team1_id }}">{{ $match->team1->name }}</option>
                                                            <option value="{{ $match->team2_id }}">{{ $match->team2->name }}</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-danger">Resolver Disputa</button>
                                                </form>
                                            @elseif($match->canBeUpdated())
                                                {{-- Formulario admin para forzar resultado --}}
                                                <details>
                                                    <summary class="btn btn-sm btn-secondary">⚙️ Admin: Forzar resultado</summary>
                                                    <form action="{{ route('torneos.updateMatch', [$tournament, $match]) }}" method="POST" class="match-result-form mt-1">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="result-inputs">
                                                            <input type="number" name="score_team1" class="score-input" placeholder="0" min="0" max="255">
                                                            <span>-</span>
                                                            <input type="number" name="score_team2" class="score-input" placeholder="0" min="0" max="255">
                                                        </div>
                                                        <div class="winner-select">
                                                            <label>Ganador:</label>
                                                            <select name="winner_id" required>
                                                                <option value="">Seleccionar...</option>
                                                                <option value="{{ $match->team1_id }}">{{ $match->team1->name }}</option>
                                                                <option value="{{ $match->team2_id }}">{{ $match->team2->name }}</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                                                    </form>
                                                </details>

                                                {{-- Formulario para programar partida --}}
                                                @if(!$match->scheduled_at)
                                                    <form action="{{ route('torneos.scheduleMatch', [$tournament, $match]) }}" method="POST" class="schedule-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                                                        <button type="submit" class="btn btn-sm btn-secondary">Programar</button>
                                                    </form>
                                                @endif
                                            @elseif($match->isBye())
                                                <span class="badge badge-secondary">Avance automático</span>
                                            @elseif($match->isCompleted())
                                                <span class="badge badge-success">Finalizado</span>
                                            @elseif(!$match->hasTeams())
                                                <span class="badge badge-warning">Esperando equipos</span>
                                            @endif
                                        </div>
                                    @endif
                                @endauth
                            </div>

                            {{-- Conector visual entre partidas (excepto última ronda) --}}
                            @if($round < $totalRounds)
                                <div class="bracket-connector"></div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endfor
    </div>

    {{-- Leyenda --}}
    <div class="bracket-legend">
        <h4>Leyenda</h4>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-color legend-winner"></span>
                <span>Ganador</span>
            </div>
            <div class="legend-item">
                <span class="legend-color legend-loser"></span>
                <span>Perdedor</span>
            </div>
            <div class="legend-item">
                <span class="legend-color legend-pending"></span>
                <span>Pendiente</span>
            </div>
            <div class="legend-item">
                <span class="legend-color legend-bye"></span>
                <span>BYE (avance automático)</span>
            </div>
            <div class="legend-item">
                <span class="legend-color legend-disputed"></span>
                <span>En disputa</span>
            </div>
        </div>
    </div>
@endsection
