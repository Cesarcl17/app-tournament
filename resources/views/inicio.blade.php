@extends('layouts.app')

@section('title', 'The Tournament Series - Torneos de eSports')

@section('content')
    <div class="hero-section">
        <h1>The Tournament Series</h1>
        <p>La plataforma definitiva para torneos de eSports. Crea tu equipo, compite contra los mejores y demuestra tu habilidad.</p>
    </div>

    <h2 class="section-title">Selecciona un juego</h2>

    <div class="games-grid">
        @foreach ($games as $game)
            <a href="{{ route('torneos.index', ['game' => $game->slug]) }}" class="game-card">
                <div class="game-card-logo">
                    @if ($game->logo)
                        <img src="{{ asset('storage/' . $game->logo) }}" alt="{{ $game->name }}">
                    @else
                        <div class="game-card-placeholder">
                            <span>{{ $game->short_name }}</span>
                        </div>
                    @endif
                </div>
                <div class="game-card-info">
                    <h3>{{ $game->name }}</h3>
                    <p class="game-card-description">{{ Str::limit($game->description, 100) }}</p>
                    <div class="game-card-stats">
                        <span class="badge badge-primary">{{ $game->tournaments_count }} torneos</span>
                        @if($game->team_sizes)
                            @foreach($game->team_sizes as $size)
                                <span class="badge badge-success">{{ $size }}v{{ $size }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if ($upcomingTournaments->isNotEmpty())
        <h2 class="section-title mt-2">Próximos torneos</h2>

        <div class="tournaments-preview">
            @foreach ($upcomingTournaments as $tournament)
                <div class="card">
                    <div class="card-header">
                        {{ $tournament->name }}
                        @if ($tournament->game)
                            <span class="badge badge-primary">{{ $tournament->game->short_name }}</span>
                        @endif
                    </div>
                    <p class="text-muted">
                        @if ($tournament->start_date)
                            Inicio: {{ \Carbon\Carbon::parse($tournament->start_date)->format('d/m/Y') }}
                        @else
                            Fecha por confirmar
                        @endif
                    </p>
                    <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-primary">Ver torneo</a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state mt-2" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 180px; text-align: center;">
            <div class="empty-state-icon" style="font-size: 2rem;">🏆</div>
            <h3 style="margin-top: 10px;">No hay torneos próximos</h3>
            <p class="text-muted" style="max-width: 400px;">Vuelve pronto para ver los nuevos torneos disponibles.</p>
            <a href="{{ route('torneos.index') }}" class="btn btn-primary" style="margin-top: 16px;">Ver todos los torneos</a>
        </div>
    @endif
@endsection
