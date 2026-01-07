@extends('layouts.app')

@section('title', 'Bracket - ' . $tournament->name)

@section('content')
    <div class="page-header">
        <h1>🏆 Bracket: {{ $tournament->name }}</h1>
        <div class="actions-inline">
            <button type="button" class="btn btn-success" id="exportBracketBtn" title="Exportar a imagen">
                📷 Exportar
            </button>
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

                                {{-- Link a comentarios --}}
                                @if($match->hasTeams() && !$match->isBye())
                                    <div class="bracket-comments-link">
                                        <a href="{{ route('matches.comments.index', $match) }}" class="comments-link">
                                            💬 Comentarios
                                            @if($match->comments->count() > 0)
                                                <span class="badge badge-secondary">{{ $match->comments->count() }}</span>
                                            @endif
                                        </a>
                                    </div>
                                @endif

                                {{-- Fecha programada --}}
                                @if($match->scheduled_at)
                                    <div class="bracket-schedule">
                                        📅 {{ $match->scheduled_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif

                                {{-- Check-in para partidas --}}
                                @if($tournament->check_in_minutes && $match->hasTeams() && !$match->isBye() && !$match->isCompleted() && $match->scheduled_at)
                                    @php
                                        $checkInStatus = $match->getCheckInStatus();
                                        $checkInOpen = $match->isCheckInOpen();
                                        $checkInExpired = $match->isCheckInExpired();
                                        $checkInMinutes = $tournament->check_in_minutes;
                                        $checkInStart = $match->scheduled_at->copy()->subMinutes($checkInMinutes);
                                    @endphp

                                    <div class="bracket-checkin">
                                        <div class="checkin-header" title="El check-in abre {{ $checkInMinutes }} minutos antes de la partida">
                                            📋 Check-in
                                            @if($checkInOpen)
                                                <span class="badge badge-success">Abierto</span>
                                            @elseif($checkInExpired)
                                                <span class="badge badge-danger">Cerrado</span>
                                            @else
                                                <span class="badge badge-secondary">Abre {{ $checkInStart->diffForHumans() }}</span>
                                            @endif
                                        </div>

                                        <div class="checkin-teams">
                                            <div class="checkin-team {{ $match->team1_checked_in ? 'checked-in' : '' }}">
                                                <span class="checkin-icon">{{ $match->team1_checked_in ? '✅' : '⏳' }}</span>
                                                <span class="checkin-name">{{ $match->team1->name }}</span>
                                            </div>
                                            <div class="checkin-team {{ $match->team2_checked_in ? 'checked-in' : '' }}">
                                                <span class="checkin-icon">{{ $match->team2_checked_in ? '✅' : '⏳' }}</span>
                                                <span class="checkin-name">{{ $match->team2->name }}</span>
                                            </div>
                                        </div>

                                        @auth
                                            @if($checkInOpen)
                                                @php
                                                    $canCheckInTeam1 = $isCaptainTeam1 && !$match->team1_checked_in;
                                                    $canCheckInTeam2 = $isCaptainTeam2 && !$match->team2_checked_in;
                                                @endphp
                                                @if($canCheckInTeam1 || $canCheckInTeam2)
                                                    <form action="{{ route('torneos.checkIn', [$tournament, $match]) }}" method="POST" class="checkin-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            ✋ Hacer Check-in
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endauth

                                        @if($match->bothTeamsCheckedIn())
                                            <div class="checkin-ready">
                                                🎮 ¡Ambos equipos listos!
                                            </div>
                                        @endif
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
    <div class="bracket-legend" id="bracketLegend">
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

    {{-- Contenedor oculto para exportar --}}
    <div id="exportContainer" style="display: none; position: fixed; left: -9999px;"></div>
@endsection

@push('scripts')
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
document.getElementById('exportBracketBtn').addEventListener('click', async function() {
    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '⏳ Generando...';
    btn.disabled = true;

    try {
        // Crear contenedor temporal para el export
        const exportDiv = document.getElementById('exportContainer');
        exportDiv.style.display = 'block';
        exportDiv.style.position = 'absolute';
        exportDiv.style.left = '-9999px';
        exportDiv.style.background = '#1a1a2e';
        exportDiv.style.padding = '20px';
        exportDiv.style.borderRadius = '8px';

        // Clonar el bracket
        const bracketContainer = document.querySelector('.bracket-container');
        const legend = document.getElementById('bracketLegend');
        const champion = document.querySelector('.champion-banner');

        // Crear header para la imagen
        const header = document.createElement('div');
        header.innerHTML = `
            <div style="text-align: center; margin-bottom: 20px; color: white;">
                <h2 style="margin: 0 0 10px 0;">🏆 {{ $tournament->name }}</h2>
                @if($tournament->game)
                    <p style="margin: 0; opacity: 0.8;">{{ $tournament->game->name }} - {{ $tournament->getFormatLabel() }}</p>
                @endif
            </div>
        `;

        exportDiv.appendChild(header);

        // Agregar banner del campeón si existe
        if (champion) {
            const championClone = champion.cloneNode(true);
            championClone.style.marginBottom = '20px';
            exportDiv.appendChild(championClone);
        }

        // Clonar y agregar el bracket
        const bracketClone = bracketContainer.cloneNode(true);

        // Remover elementos interactivos del clon
        bracketClone.querySelectorAll('form, details, .match-actions, button, .schedule-form').forEach(el => el.remove());
        bracketClone.querySelectorAll('.badge-primary').forEach(el => {
            if (el.textContent.trim() === 'Tu equipo') el.remove();
        });

        exportDiv.appendChild(bracketClone);

        // Agregar leyenda
        const legendClone = legend.cloneNode(true);
        legendClone.style.marginTop = '20px';
        exportDiv.appendChild(legendClone);

        // Agregar footer con fecha
        const footer = document.createElement('div');
        footer.innerHTML = `
            <div style="text-align: center; margin-top: 20px; color: rgba(255,255,255,0.5); font-size: 12px;">
                Generado el ${new Date().toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })}
            </div>
        `;
        exportDiv.appendChild(footer);

        // Esperar un momento para que se renderice
        await new Promise(resolve => setTimeout(resolve, 100));

        // Generar imagen
        const canvas = await html2canvas(exportDiv, {
            backgroundColor: '#1a1a2e',
            scale: 2,
            logging: false,
            useCORS: true
        });

        // Limpiar contenedor temporal
        exportDiv.innerHTML = '';
        exportDiv.style.display = 'none';

        // Descargar imagen
        const link = document.createElement('a');
        const tournamentSlug = '{{ Str::slug($tournament->name) }}';
        link.download = `bracket-${tournamentSlug}-${new Date().toISOString().split('T')[0]}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();

        btn.innerHTML = '✅ Descargado';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 2000);

    } catch (error) {
        console.error('Error al exportar bracket:', error);
        btn.innerHTML = '❌ Error';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 2000);
    }
});
</script>
@endpush

@push('styles')
<style>
    .bracket-comments-link {
        margin-top: 0.5rem;
    }

    .comments-link {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.85rem;
        color: #6c757d;
        text-decoration: none;
    }

    .comments-link:hover {
        color: #007bff;
    }

    .comments-link .badge {
        font-size: 0.7rem;
        padding: 0.15rem 0.4rem;
    }
</style>
@endpush
