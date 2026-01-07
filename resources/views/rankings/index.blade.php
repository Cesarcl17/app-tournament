@extends('layouts.app')

@section('title', 'Rankings')

@section('content')
    <div class="page-header">
        <h2>🏆 Rankings</h2>
    </div>

    {{-- Tabs de tipo --}}
    <div class="ranking-tabs mb-2">
        <a href="{{ route('rankings.index', ['type' => 'players', 'sort' => $sortBy]) }}" 
           class="tab-btn {{ $type === 'players' ? 'active' : '' }}">
            👤 Jugadores
        </a>
        <a href="{{ route('rankings.index', ['type' => 'teams', 'sort' => $sortBy]) }}" 
           class="tab-btn {{ $type === 'teams' ? 'active' : '' }}">
            👥 Equipos
        </a>
    </div>

    {{-- Filtros --}}
    <div class="ranking-filters mb-2">
        <div class="filter-group">
            <label>Ordenar por:</label>
            <select onchange="window.location.href=this.value" class="form-control">
                <option value="{{ route('rankings.index', ['type' => $type, 'sort' => 'wins', 'game' => $gameFilter]) }}" {{ $sortBy === 'wins' ? 'selected' : '' }}>
                    Victorias totales
                </option>
                <option value="{{ route('rankings.index', ['type' => $type, 'sort' => 'win_rate', 'game' => $gameFilter]) }}" {{ $sortBy === 'win_rate' ? 'selected' : '' }}>
                    % Victoria (mín. 5 partidas)
                </option>
                <option value="{{ route('rankings.index', ['type' => $type, 'sort' => 'streak', 'game' => $gameFilter]) }}" {{ $sortBy === 'streak' ? 'selected' : '' }}>
                    Mejor racha
                </option>
                <option value="{{ route('rankings.index', ['type' => $type, 'sort' => 'tournaments', 'game' => $gameFilter]) }}" {{ $sortBy === 'tournaments' ? 'selected' : '' }}>
                    Torneos ganados
                </option>
            </select>
        </div>

        @if($type === 'teams')
            <div class="filter-group">
                <label>Juego:</label>
                <select onchange="window.location.href=this.value" class="form-control">
                    <option value="{{ route('rankings.index', ['type' => $type, 'sort' => $sortBy]) }}" {{ !$gameFilter ? 'selected' : '' }}>
                        Todos
                    </option>
                    @foreach($games as $game)
                        <option value="{{ route('rankings.index', ['type' => $type, 'sort' => $sortBy, 'game' => $game->slug]) }}" 
                                {{ $gameFilter === $game->slug ? 'selected' : '' }}>
                            {{ $game->short_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Tabla de Rankings --}}
    @if($rankings->isEmpty())
        <div class="empty-state">
            <p class="text-muted">No hay estadísticas disponibles todavía.</p>
            <p class="text-muted">Los rankings se actualizarán cuando se jueguen partidas.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table ranking-table">
                <thead>
                    <tr>
                        <th class="rank-col">#</th>
                        <th>{{ $type === 'players' ? 'Jugador' : 'Equipo' }}</th>
                        @if($type === 'teams')
                            <th>Juego</th>
                        @endif
                        <th class="text-center">V</th>
                        <th class="text-center">D</th>
                        <th class="text-center">Partidas</th>
                        <th class="text-center">% Win</th>
                        <th class="text-center">Racha</th>
                        <th class="text-center">🏆</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankings as $index => $stat)
                        <tr class="{{ $index < 3 ? 'top-rank rank-' . ($index + 1) : '' }}">
                            <td class="rank-col">
                                @if($index === 0)
                                    <span class="rank-medal">🥇</span>
                                @elseif($index === 1)
                                    <span class="rank-medal">🥈</span>
                                @elseif($index === 2)
                                    <span class="rank-medal">🥉</span>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>
                                @if($type === 'players')
                                    <a href="{{ route('users.show', $stat->user) }}">
                                        {{ $stat->user->name }}
                                    </a>
                                @else
                                    <a href="{{ route('teams.show', $stat->team) }}">
                                        @if($stat->team->logo)
                                            <img src="{{ Storage::url($stat->team->logo) }}" alt="" class="team-logo-small">
                                        @endif
                                        {{ $stat->team->name }}
                                    </a>
                                @endif
                            </td>
                            @if($type === 'teams')
                                <td>
                                    @if($stat->team->tournament && $stat->team->tournament->game)
                                        <span class="badge badge-primary">{{ $stat->team->tournament->game->short_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endif
                            <td class="text-center text-success fw-bold">{{ $stat->wins }}</td>
                            <td class="text-center text-danger">{{ $stat->losses }}</td>
                            <td class="text-center">{{ $stat->matches_played }}</td>
                            <td class="text-center">
                                <span class="win-rate {{ $stat->win_rate >= 60 ? 'high' : ($stat->win_rate >= 40 ? 'medium' : 'low') }}">
                                    {{ $stat->win_rate }}%
                                </span>
                            </td>
                            <td class="text-center">
                                @if($stat->current_win_streak > 0)
                                    <span class="streak active">🔥 {{ $stat->current_win_streak }}</span>
                                @else
                                    <span class="text-muted">{{ $stat->best_win_streak }}</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $stat->tournaments_won }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

@push('styles')
<style>
    .team-logo-small {
        width: 28px;
        height: 28px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 0.5rem;
        vertical-align: middle;
        border: 1px solid var(--border-color);
    }

    .win-rate {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
    }

    .win-rate.high { 
        background: rgba(0, 184, 148, 0.15);
        color: var(--color-success); 
    }
    .win-rate.medium { 
        background: rgba(243, 156, 18, 0.15);
        color: var(--color-warning); 
    }
    .win-rate.low { 
        background: rgba(231, 76, 60, 0.15);
        color: var(--color-danger); 
    }

    .streak.active {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 50px;
        background: rgba(253, 126, 20, 0.15);
        color: #fd7e14;
        font-weight: 700;
    }

    .fw-bold { font-weight: 700; }

    /* Empty state usa estilos globales */
</style>
@endpush
