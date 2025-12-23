@extends('layouts.app')

@section('title', 'Disputas - ' . $tournament->name)

@section('content')
    <div class="page-header">
        <h1>⚠️ Disputas: {{ $tournament->name }}</h1>
        <div class="actions-inline">
            <a href="{{ route('torneos.bracket', $tournament) }}" class="btn btn-secondary">Volver al bracket</a>
            <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-secondary">Ver torneo</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="disputes-container">
        @if($disputes->isEmpty())
            <div class="alert alert-info">
                ✅ No hay disputas pendientes en este torneo.
            </div>
        @else
            <p class="text-muted mb-3">
                Las siguientes partidas tienen resultados reportados que no coinciden.
                Como administrador, debes revisar las evidencias y establecer el resultado correcto.
            </p>

            @foreach($disputes as $match)
                <div class="dispute-card">
                    <h4>
                        Ronda {{ $match->round }} - Partida #{{ $match->match_number }}
                    </h4>

                    <div class="dispute-reports">
                        <div class="dispute-report">
                            <h5>📝 Reporte de {{ $match->team1->name }}</h5>
                            <div class="dispute-score">
                                {{ $match->score_team1_by_captain1 }} - {{ $match->score_team2_by_captain1 }}
                            </div>
                            <p class="text-muted text-center" style="font-size: 0.8rem; margin-top: 5px;">
                                @if($match->score_team1_by_captain1 > $match->score_team2_by_captain1)
                                    Reporta victoria propia
                                @elseif($match->score_team1_by_captain1 < $match->score_team2_by_captain1)
                                    Reporta derrota
                                @else
                                    Reporta empate
                                @endif
                            </p>
                        </div>

                        <div class="dispute-report">
                            <h5>📝 Reporte de {{ $match->team2->name }}</h5>
                            <div class="dispute-score">
                                {{ $match->score_team1_by_captain2 }} - {{ $match->score_team2_by_captain2 }}
                            </div>
                            <p class="text-muted text-center" style="font-size: 0.8rem; margin-top: 5px;">
                                @if($match->score_team2_by_captain2 > $match->score_team1_by_captain2)
                                    Reporta victoria propia
                                @elseif($match->score_team2_by_captain2 < $match->score_team1_by_captain2)
                                    Reporta derrota
                                @else
                                    Reporta empate
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="dispute-actions">
                        <h5 style="margin-bottom: 10px;">Resolver disputa:</h5>
                        <form action="{{ route('torneos.resolveDispute', [$tournament, $match]) }}" method="POST" class="match-result-form">
                            @csrf
                            <div class="result-inputs" style="margin-bottom: 15px;">
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

                            <div class="winner-select" style="margin-bottom: 15px;">
                                <label>Selecciona el ganador oficial:</label>
                                <select name="winner_id" required class="form-control">
                                    <option value="">-- Seleccionar ganador --</option>
                                    <option value="{{ $match->team1_id }}">{{ $match->team1->name }}</option>
                                    <option value="{{ $match->team2_id }}">{{ $match->team2->name }}</option>
                                </select>
                            </div>

                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger">
                                    ✓ Resolver y establecer resultado
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="acceptReport(this, 1, {{ $match->score_team1_by_captain1 }}, {{ $match->score_team2_by_captain1 }})">
                                    Aceptar reporte Equipo 1
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="acceptReport(this, 2, {{ $match->score_team1_by_captain2 }}, {{ $match->score_team2_by_captain2 }})">
                                    Aceptar reporte Equipo 2
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        function acceptReport(btn, team, score1, score2) {
            const form = btn.closest('form');
            form.querySelector('input[name="score_team1"]').value = score1;
            form.querySelector('input[name="score_team2"]').value = score2;

            const select = form.querySelector('select[name="winner_id"]');
            if (score1 > score2) {
                select.selectedIndex = 1; // Team 1
            } else if (score2 > score1) {
                select.selectedIndex = 2; // Team 2
            }
        }
    </script>
@endsection
