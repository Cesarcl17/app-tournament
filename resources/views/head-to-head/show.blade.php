@extends('layouts.app')

@section('title', $team1->name . ' vs ' . $team2->name . ' - Head to Head')

@section('content')
    <div class="page-header">
        <h1>⚔️ Head to Head</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
    </div>

    {{-- Banner de los equipos --}}
    <div class="h2h-banner">
        <div class="h2h-team h2h-team-left {{ $history['team1_wins'] > $history['team2_wins'] ? 'leading' : '' }}">
            <a href="{{ route('teams.show', $team1) }}" class="team-name">{{ $team1->name }}</a>
            @if($team1->tournament)
                <span class="team-tournament">{{ $team1->tournament->name }}</span>
            @endif
        </div>

        <div class="h2h-score">
            <div class="score-numbers">
                <span class="wins {{ $history['team1_wins'] > $history['team2_wins'] ? 'leading' : '' }}">{{ $history['team1_wins'] }}</span>
                <span class="separator">-</span>
                <span class="wins {{ $history['team2_wins'] > $history['team1_wins'] ? 'leading' : '' }}">{{ $history['team2_wins'] }}</span>
            </div>
            <div class="total-matches">
                {{ $history['total_matches'] }} {{ Str::plural('enfrentamiento', $history['total_matches']) }}
            </div>
        </div>

        <div class="h2h-team h2h-team-right {{ $history['team2_wins'] > $history['team1_wins'] ? 'leading' : '' }}">
            <a href="{{ route('teams.show', $team2) }}" class="team-name">{{ $team2->name }}</a>
            @if($team2->tournament)
                <span class="team-tournament">{{ $team2->tournament->name }}</span>
            @endif
        </div>
    </div>

    {{-- Estadísticas adicionales --}}
    @if($history['total_matches'] > 0)
        <div class="h2h-stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['avg_score_team1'] }}</div>
                <div class="stat-label">Promedio de puntos<br><strong>{{ $team1->name }}</strong></div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['tournaments_faced'] }}</div>
                <div class="stat-label">Torneos donde<br>se enfrentaron</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['avg_score_team2'] }}</div>
                <div class="stat-label">Promedio de puntos<br><strong>{{ $team2->name }}</strong></div>
            </div>
        </div>

        {{-- Historial de partidas --}}
        <div class="card mt-2">
            <div class="card-header">📜 Historial de enfrentamientos</div>

            <div class="matches-list">
                @foreach($history['matches'] as $match)
                    @php
                        $isTeam1Winner = $match->winner_id === $team1->id;
                        $team1Score = $match->team1_id === $team1->id ? $match->score_team1 : $match->score_team2;
                        $team2Score = $match->team1_id === $team1->id ? $match->score_team2 : $match->score_team1;
                    @endphp
                    <div class="match-row {{ $isTeam1Winner ? 'team1-won' : 'team2-won' }}">
                        <div class="match-tournament">
                            @if($match->tournament)
                                <a href="{{ route('torneos.show', $match->tournament) }}">
                                    {{ $match->tournament->name }}
                                </a>
                                @if($match->tournament->game)
                                    <span class="badge badge-primary">{{ $match->tournament->game->short_name }}</span>
                                @endif
                            @endif
                        </div>
                        <div class="match-result">
                            <span class="team {{ $isTeam1Winner ? 'winner' : 'loser' }}">{{ $team1->name }}</span>
                            <span class="score">
                                <span class="{{ $isTeam1Winner ? 'winner' : 'loser' }}">{{ $team1Score ?? '-' }}</span>
                                <span class="separator">:</span>
                                <span class="{{ !$isTeam1Winner ? 'winner' : 'loser' }}">{{ $team2Score ?? '-' }}</span>
                            </span>
                            <span class="team {{ !$isTeam1Winner ? 'winner' : 'loser' }}">{{ $team2->name }}</span>
                        </div>
                        <div class="match-date">
                            @if($match->played_at)
                                {{ \Carbon\Carbon::parse($match->played_at)->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="empty-state mt-2">
            <p class="text-muted">Estos equipos nunca se han enfrentado.</p>
        </div>
    @endif
@endsection

@push('styles')
<style>
    .h2h-banner {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        padding: 2rem;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .h2h-team {
        flex: 1;
        text-align: center;
    }

    .h2h-team-left {
        text-align: right;
    }

    .h2h-team-right {
        text-align: left;
    }

    .h2h-team .team-name {
        display: block;
        font-size: 1.5rem;
        font-weight: bold;
        color: #fff;
        text-decoration: none;
    }

    .h2h-team .team-name:hover {
        color: #4f8ef7;
    }

    .h2h-team.leading .team-name {
        color: #ffc107;
    }

    .h2h-team .team-tournament {
        font-size: 0.9rem;
        color: rgba(255,255,255,0.6);
    }

    .h2h-score {
        text-align: center;
        padding: 0 2rem;
    }

    .score-numbers {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 3rem;
        font-weight: bold;
    }

    .score-numbers .wins {
        color: #fff;
    }

    .score-numbers .wins.leading {
        color: #28a745;
    }

    .score-numbers .separator {
        color: rgba(255,255,255,0.5);
    }

    .total-matches {
        color: rgba(255,255,255,0.6);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .h2h-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--bg-secondary, #f8f9fa);
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #007bff;
    }

    .stat-card .stat-label {
        font-size: 0.9rem;
        color: #666;
        margin-top: 0.5rem;
    }

    .matches-list {
        padding: 0;
    }

    .match-row {
        display: grid;
        grid-template-columns: 1fr 2fr auto;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid #eee;
        align-items: center;
    }

    .match-row:last-child {
        border-bottom: none;
    }

    .match-tournament a {
        font-weight: 500;
        color: #007bff;
        text-decoration: none;
    }

    .match-result {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
    }

    .match-result .team {
        min-width: 150px;
    }

    .match-result .team:first-child {
        text-align: right;
    }

    .match-result .team:last-child {
        text-align: left;
    }

    .match-result .score {
        display: flex;
        gap: 0.25rem;
        font-weight: bold;
        font-size: 1.25rem;
    }

    .match-result .winner {
        color: #28a745;
        font-weight: bold;
    }

    .match-result .loser {
        color: #999;
    }

    .match-date {
        color: #666;
        font-size: 0.9rem;
    }

    /* Empty state usa estilos globales */

    .mt-2 { margin-top: 1rem; }

    @media (max-width: 768px) {
        .h2h-banner {
            flex-direction: column;
            gap: 1rem;
        }

        .h2h-team {
            text-align: center !important;
        }

        .h2h-stats-grid {
            grid-template-columns: 1fr;
        }

        .match-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .match-result {
            flex-direction: column;
            gap: 0.5rem;
        }

        .match-result .team {
            min-width: auto;
            text-align: center !important;
        }
    }
</style>
@endpush
