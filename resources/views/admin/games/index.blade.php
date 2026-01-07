@extends('layouts.app')

@section('title', 'Gestión de Juegos - Admin')

@section('content')
<div class="page-header">
    <h1>🎮 Gestión de Juegos</h1>
    <a href="{{ route('admin.games.create') }}" class="btn btn-primary">
        ➕ Nuevo Juego
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($games->count() > 0)
    <div class="games-admin-grid">
        @foreach($games as $game)
            <div class="game-admin-card">
                <div class="game-admin-logo">
                    @if($game->logo)
                        <img src="{{ asset('storage/' . $game->logo) }}" alt="{{ $game->name }}">
                    @else
                        <div class="game-admin-placeholder">
                            {{ strtoupper(substr($game->short_name, 0, 2)) }}
                        </div>
                    @endif
                    @if(!$game->active)
                        <span class="game-inactive-badge">Inactivo</span>
                    @endif
                </div>
                <div class="game-admin-info">
                    <h3>{{ $game->name }}</h3>
                    <p class="game-admin-meta">
                        <span class="badge badge-secondary">{{ $game->short_name }}</span>
                        <span class="badge badge-primary">{{ $game->team_size }}v{{ $game->team_size }}</span>
                    </p>
                    @if($game->description)
                        <p class="game-admin-description">{{ Str::limit($game->description, 80) }}</p>
                    @endif
                    <p class="game-admin-stats">
                        <span>🏆 {{ $game->tournaments_count }} torneos</span>
                    </p>
                </div>
                <div class="game-admin-actions">
                    <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-sm btn-secondary" title="Editar">
                        ✏️
                    </a>
                    @if($game->tournaments_count == 0)
                        <form action="{{ route('admin.games.destroy', $game) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este juego?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                🗑️
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="pagination-container">
        {{ $games->links() }}
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-icon">🎮</div>
        <h3>No hay juegos registrados</h3>
        <p>Crea tu primer juego para empezar a organizar torneos.</p>
        <a href="{{ route('admin.games.create') }}" class="btn btn-primary">
            ➕ Crear primer juego
        </a>
    </div>
@endif
@endsection
