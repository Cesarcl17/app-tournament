@extends('layouts.app')

@section('title', 'Editar Juego - Admin')

@section('content')
<div class="page-header">
    <h1>✏️ Editar Juego: {{ $game->name }}</h1>
    <a href="{{ route('admin.games.index') }}" class="btn btn-secondary">
        ← Volver al listado
    </a>
</div>

<div class="card">
    <div class="card-header">
        📝 Información del Juego
    </div>

    <form action="{{ route('admin.games.update', $game) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nombre del Juego *</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $game->name) }}" placeholder="Ej: League of Legends" required>
                @error('name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="short_name">Nombre Corto *</label>
                <input type="text" id="short_name" name="short_name" class="form-control @error('short_name') is-invalid @enderror" 
                       value="{{ old('short_name', $game->short_name) }}" placeholder="Ej: LoL" required>
                @error('short_name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
                <small class="form-hint">Se usa para badges y referencias rápidas</small>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" 
                      rows="3" placeholder="Descripción breve del juego...">{{ old('description', $game->description) }}</textarea>
            @error('description')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Formatos de Equipo * <small class="form-hint">(Selecciona todos los que apliquen)</small></label>
            <div class="checkbox-grid">
                @php
                    $teamSizeOptions = [
                        1 => '1v1',
                        2 => '2v2',
                        3 => '3v3',
                        4 => '4v4',
                        5 => '5v5',
                        6 => '6v6',
                    ];
                    $currentSizes = old('team_sizes', $game->team_sizes ?? [5]);
                @endphp
                @foreach($teamSizeOptions as $value => $label)
                    <label class="checkbox-card">
                        <input type="checkbox" name="team_sizes[]" value="{{ $value }}" 
                               {{ in_array($value, (array)$currentSizes) ? 'checked' : '' }}>
                        <span class="checkbox-card-label">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('team_sizes')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="positions">Posiciones/Roles del Juego</label>
            <input type="text" id="positions" name="positions" class="form-control @error('positions') is-invalid @enderror" 
                   value="{{ old('positions', $game->positions ? implode(', ', $game->positions) : '') }}"
                   placeholder="Ej: Top, Jungle, Mid, ADC, Support">
            @error('positions')
                <span class="error-text">{{ $message }}</span>
            @enderror
            <small class="form-hint">Separadas por comas. Estas son las posiciones que los jugadores pueden elegir.</small>
        </div>

        <div class="form-group">
            <label for="logo">Logo del Juego</label>
            <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror" 
                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
            @error('logo')
                <span class="error-text">{{ $message }}</span>
            @enderror
            <small class="form-hint">Formatos: JPG, PNG, GIF, WebP. Máx: 5MB</small>
        </div>

        <div class="form-group">
            @if($game->logo)
                <div class="current-image">
                    <label>Logo actual:</label>
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $game->logo) }}" alt="{{ $game->name }}">
                        <a href="#" class="btn btn-sm btn-danger" 
                           onclick="event.preventDefault(); if(confirm('¿Eliminar logo?')) document.getElementById('delete-logo-form').submit();">
                            🗑️ Eliminar logo
                        </a>
                    </div>
                </div>
            @endif
            <div id="logo-preview" class="image-preview" style="display: none;">
                <label>Nuevo logo:</label>
                <img id="logo-preview-img" src="" alt="Vista previa">
                <button type="button" class="btn btn-sm btn-danger" onclick="clearLogoPreview()">✕ Quitar</button>
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="active" value="1" {{ old('active', $game->active) ? 'checked' : '' }}>
                <span>Juego activo</span>
            </label>
            <small class="form-hint">Los juegos inactivos no aparecen para crear torneos</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                ✓ Guardar Cambios
            </button>
            <a href="{{ route('admin.games.index') }}" class="btn btn-secondary">
                Cancelar
            </a>
        </div>
    </form>
    
    @if($game->logo)
    <form id="delete-logo-form" action="{{ route('admin.games.delete-logo', $game) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @endif
</div>

<div class="card mt-2">
    <div class="card-header">
        📊 Estadísticas del Juego
    </div>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value">{{ $game->tournaments()->count() }}</div>
            <div class="stat-label">Torneos Totales</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $game->activeTournaments()->count() }}</div>
            <div class="stat-label">Torneos Activos</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $game->created_at->format('d/m/Y') }}</div>
            <div class="stat-label">Fecha de Creación</div>
        </div>
    </div>
</div>

<script>
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo-preview-img').src = e.target.result;
            document.getElementById('logo-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function clearLogoPreview() {
    document.getElementById('logo').value = '';
    document.getElementById('logo-preview').style.display = 'none';
    document.getElementById('logo-preview-img').src = '';
}
</script>
@endsection
