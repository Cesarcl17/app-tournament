@extends('layouts.app')

@section('title', 'Crear equipo')

@section('content')
    <div class="page-header">
        <h1>Crear equipo para: {{ $tournament->name }}</h1>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('teams.store', $tournament) }}">
            @csrf

            <div class="form-group">
                <label for="name">Nombre del equipo</label>
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

            <div class="actions">
                <button type="submit" class="btn btn-primary">Crear equipo</button>
                <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
