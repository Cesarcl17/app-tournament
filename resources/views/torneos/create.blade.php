@extends('layouts.app')

@section('title', 'Crear torneo')

@section('content')
    <div class="page-header">
        <h2>Crear torneo{{ isset($selectedGame) ? ' de ' . $selectedGame->name : '' }}</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('torneos.store') }}">
            @csrf

            <div class="form-group">
                <label for="game_id">Juego</label>
                <select id="game_id"
                        name="game_id"
                        class="form-control @error('game_id') is-invalid @enderror"
                        required>
                    <option value="">-- Selecciona un juego --</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ (old('game_id', $selectedGame?->id) == $game->id) ? 'selected' : '' }}>
                            {{ $game->name }} ({{ $game->team_size }}v{{ $game->team_size }})
                        </option>
                    @endforeach
                </select>
                @error('game_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="team_size">Formato</label>
                <select id="team_size"
                        name="team_size"
                        class="form-control @error('team_size') is-invalid @enderror"
                        required>
                    <option value="5" {{ old('team_size', 5) == 5 ? 'selected' : '' }}>5v5 (Equipo completo)</option>
                    <option value="3" {{ old('team_size') == 3 ? 'selected' : '' }}>3v3 (Equipo reducido)</option>
                    <option value="1" {{ old('team_size') == 1 ? 'selected' : '' }}>1v1 (Individual)</option>
                </select>
                @error('team_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description"
                          name="description"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="start_date">Fecha inicio</label>
                <input type="date"
                       id="start_date"
                       name="start_date"
                       class="form-control @error('start_date') is-invalid @enderror"
                       value="{{ old('start_date') }}"
                       required>
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_date">Fecha fin</label>
                <input type="date"
                       id="end_date"
                       name="end_date"
                       class="form-control @error('end_date') is-invalid @enderror"
                       value="{{ old('end_date') }}"
                       required>
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Crear torneo</button>
                <a href="{{ route('torneos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
