@extends('layouts.app')

@section('title', $user->name . ' - Perfil')

@section('content')
    <div class="page-header">
        <h1>{{ $user->name }}</h1>
        <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'captain' ? 'primary' : 'success') }}">
            {{ ucfirst($user->role) }}
        </span>
    </div>

    <div class="profile-grid">
        {{-- Columna izquierda: Info y Stats --}}
        <div class="profile-main">
            {{-- Estadísticas --}}
            <div class="card">
                <div class="card-header">📊 Estadísticas</div>

                @if($user->statistics)
                    @php $stats = $user->statistics; @endphp
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats->matches_played }}</div>
                            <div class="stat-label">Partidas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value text-success">{{ $stats->wins }}</div>
                            <div class="stat-label">Victorias</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value text-danger">{{ $stats->losses }}</div>
                            <div class="stat-label">Derrotas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats->win_rate }}%</div>
                            <div class="stat-label">Win Rate</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats->tournaments_played }}</div>
                            <div class="stat-label">Torneos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value text-warning">🏆 {{ $stats->tournaments_won }}</div>
                            <div class="stat-label">Títulos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">🔥 {{ $stats->current_win_streak }}</div>
                            <div class="stat-label">Racha</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">⭐ {{ $stats->best_win_streak }}</div>
                            <div class="stat-label">Mejor Racha</div>
                        </div>
                    </div>
                @else
                    <div class="empty-state-inline">
                        <span class="empty-icon">📊</span>
                        <p>Este jugador aún no tiene estadísticas.</p>
                    </div>
                @endif
            </div>

            {{-- Trofeos --}}
            <div class="card mt-2">
                <div class="card-header">🏆 Trofeos ({{ $user->trophies->count() }})</div>

                @if($user->trophies->isEmpty())
                    <div class="empty-state-inline">
                        <span class="empty-icon">🏆</span>
                        <p>Este jugador aún no tiene trofeos. ¡A competir!</p>
                    </div>
                @else
                    <div class="trophies-grid">
                        @foreach($user->trophies as $trophy)
                            <div class="trophy-item">
                                <div class="trophy-icon">
                                    @if($trophy->game && $trophy->game->logo)
                                        <img src="{{ asset('images/games/' . $trophy->game->logo) }}"
                                             alt="{{ $trophy->game->name }}"
                                             class="trophy-game-logo">
                                    @else
                                        🏆
                                    @endif
                                </div>
                                <div class="trophy-info">
                                    <div class="trophy-name">{{ $trophy->name }}</div>
                                    @if($trophy->description)
                                        <div class="trophy-description text-muted">{{ $trophy->description }}</div>
                                    @endif
                                    <div class="trophy-date">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($trophy->pivot->earned_at)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Columna derecha: Equipos y Torneos --}}
        <div class="profile-sidebar">
            {{-- Equipos Actuales --}}
            <div class="card">
                <div class="card-header">👥 Equipos Actuales</div>

                @if($activeTeams->isEmpty())
                    <div class="empty-state-inline">
                        <span class="empty-icon">👥</span>
                        <p>No está en ningún equipo activo.</p>
                    </div>
                @else
                    <ul class="team-list">
                        @foreach($activeTeams as $team)
                            <li class="team-list-item">
                                <a href="{{ route('teams.show', $team) }}" class="team-name">
                                    {{ $team->name }}
                                </a>
                                <div class="team-meta">
                                    <a href="{{ route('torneos.show', $team->tournament) }}">
                                        {{ $team->tournament->name }}
                                    </a>
                                    @if($team->pivot->role === 'captain')
                                        <span class="badge badge-primary">Capitán</span>
                                    @endif
                                </div>
                                @if($team->tournament->game)
                                    <small class="text-muted">{{ $team->tournament->game->name }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Historial de Torneos --}}
            <div class="card mt-2">
                <div class="card-header">📜 Historial de Torneos</div>

                @if($finishedTeams->isEmpty())
                    <div class="empty-state-inline">
                        <span class="empty-icon">📜</span>
                        <p>No ha participado en torneos finalizados.</p>
                    </div>
                @else
                    <ul class="tournament-history">
                        @foreach($finishedTeams as $team)
                            @php
                                $result = $tournamentResults[$team->tournament->id] ?? null;
                            @endphp
                            <li class="history-item {{ $result && $result['is_champion'] ? 'champion' : '' }}">
                                <div class="history-header">
                                    <a href="{{ route('torneos.show', $team->tournament) }}" class="tournament-name">
                                        {{ $team->tournament->name }}
                                    </a>
                                    @if($result)
                                        <span class="position {{ $result['is_champion'] ? 'champion' : '' }}">
                                            {{ $result['position'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="history-meta">
                                    <span class="team-name">{{ $team->name }}</span>
                                    @if($team->tournament->game)
                                        <span class="game-name">{{ $team->tournament->game->name }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $team->tournament->end_date->format('d/m/Y') }}
                                </small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        padding: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 0.75rem;
        background: var(--bg-secondary, #f8f9fa);
        border-radius: 8px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #666;
    }

    .text-success { color: #28a745; }
    .text-danger { color: #dc3545; }
    .text-warning { color: #ffc107; }

    .trophies-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
        padding: 1rem;
    }

    .trophy-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
        border: 1px solid #ffc107;
        border-radius: 8px;
    }

    .trophy-icon {
        font-size: 1.75rem;
    }

    .trophy-game-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 4px;
    }

    .trophy-name {
        font-weight: bold;
        font-size: 0.9rem;
    }

    .team-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .team-list-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eee;
    }

    .team-list-item:last-child {
        border-bottom: none;
    }

    .team-list-item .team-name {
        font-weight: bold;
        color: #007bff;
        text-decoration: none;
    }

    .team-list-item .team-name:hover {
        text-decoration: underline;
    }

    .team-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.25rem;
        font-size: 0.9rem;
    }

    .tournament-history {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .history-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eee;
    }

    .history-item:last-child {
        border-bottom: none;
    }

    .history-item.champion {
        background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-header .tournament-name {
        font-weight: bold;
        color: #007bff;
        text-decoration: none;
    }

    .history-header .position {
        font-size: 0.85rem;
        font-weight: bold;
    }

    .history-header .position.champion {
        color: #ffc107;
    }

    .history-meta {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.25rem;
        font-size: 0.85rem;
        color: #666;
    }

    .history-meta .team-name::after {
        content: '•';
        margin-left: 0.5rem;
    }

    .mt-2 {
        margin-top: 1rem;
    }

    .p-1 {
        padding: 1rem;
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush
