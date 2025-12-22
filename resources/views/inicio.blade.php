@extends('layouts.app')

@section('title', 'Inicio - App Tournament')

@section('content')
    <div class="hero-section">
        <h1>Torneos de eSports</h1>
        <p>Únete a los mejores torneos de tus juegos favoritos. Crea tu equipo, compite y demuestra tu habilidad.</p>
    </div>

    <h2 class="section-title">Selecciona un juego</h2>

    <div class="games-grid">
        @foreach ($games as $game)
            <a href="{{ route('torneos.index', ['game' => $game->slug]) }}" class="game-card">
                <div class="game-card-logo">
                    @if ($game->logo && file_exists(public_path($game->logo)))
                        <img src="{{ asset($game->logo) }}" alt="{{ $game->name }}">
                    @else
                        <div class="game-card-placeholder">
                            <span>{{ $game->short_name }}</span>
                        </div>
                    @endif
                </div>
                <div class="game-card-info">
                    <h3>{{ $game->name }}</h3>
                    <p class="game-card-description">{{ $game->description }}</p>
                    <div class="game-card-stats">
                        <span class="badge badge-primary">{{ $game->tournaments_count }} torneos</span>
                        <span class="badge badge-success">1v1</span>
                        <span class="badge badge-success">3v3</span>
                        <span class="badge badge-success">5v5</span>
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
    @endif
@endsection
