@extends('layouts.app')

@section('title', 'Editar equipo')

@section('content')
    <div class="page-header">
        <h1>Editar equipo</h1>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('teams.update', $team) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $team->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description"
                          name="description"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $team->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('torneos.show', $team->tournament_id) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
