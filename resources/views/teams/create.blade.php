@extends('layouts.app')

@section('title', 'Crear equipo')

@section('content')
    <div class="page-header">
        <h1>Crear equipo para: {{ $tournament->name }}</h1>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('teams.store', $tournament) }}" enctype="multipart/form-data">
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

            <div class="form-group">
                <label for="logo">Logo del equipo</label>
                <input type="file"
                       id="logo"
                       name="logo"
                       class="form-control @error('logo') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Formatos: JPG, PNG, GIF, WebP. Máx: 5MB</small>
            </div>

            <div class="form-group">
                <div id="logo-preview" class="image-preview" style="display: none;">
                    <img id="logo-preview-img" src="" alt="Vista previa">
                    <button type="button" class="btn btn-sm btn-danger" onclick="clearLogoPreview()">✕ Quitar</button>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Crear equipo</button>
                <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
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
