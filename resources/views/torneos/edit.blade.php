@extends('layouts.app')

@section('title', 'Editar torneo')

@section('content')
    <div class="page-header">
        <h1>Editar torneo: {{ $tournament->name }}</h1>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('torneos.update', $tournament) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="game_id">Juego</label>
                <select id="game_id"
                        name="game_id"
                        class="form-control @error('game_id') is-invalid @enderror"
                        required>
                    <option value="">-- Selecciona un juego --</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ old('game_id', $tournament->game_id) == $game->id ? 'selected' : '' }}>
                            {{ $game->name }}
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
                    <option value="5" {{ old('team_size', $tournament->team_size) == 5 ? 'selected' : '' }}>5v5 (Equipo completo)</option>
                    <option value="3" {{ old('team_size', $tournament->team_size) == 3 ? 'selected' : '' }}>3v3 (Equipo reducido)</option>
                    <option value="1" {{ old('team_size', $tournament->team_size) == 1 ? 'selected' : '' }}>1v1 (Individual)</option>
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
                       value="{{ old('name', $tournament->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description"
                          name="description"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $tournament->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="banner">Banner del Torneo</label>
                <input type="file"
                       id="banner"
                       name="banner"
                       class="form-control @error('banner') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                @error('banner')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Formatos: JPG, PNG, GIF, WebP. Máx: 10MB</small>
            </div>

            <div class="form-group">
                @if($tournament->banner)
                    <div class="current-image">
                        <label>Banner actual:</label>
                        <div class="image-preview">
                            <img src="{{ asset('storage/' . $tournament->banner) }}" alt="{{ $tournament->name }}">
                            <form action="{{ route('torneos.delete-banner', $tournament) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar banner?')">
                                    🗑️ Eliminar banner
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
                <div id="banner-preview" class="image-preview" style="display: none;">
                    <label>Nuevo banner:</label>
                    <img id="banner-preview-img" src="" alt="Vista previa">
                    <button type="button" class="btn btn-sm btn-danger" onclick="clearBannerPreview()">✕ Quitar</button>
                </div>
            </div>

            <div class="form-group">
                <label for="rules">Reglas del Torneo</label>
                <textarea id="rules"
                          name="rules"
                          class="form-control @error('rules') is-invalid @enderror"
                          placeholder="Describe las reglas y normas del torneo..."
                          rows="5">{{ old('rules', $tournament->rules) }}</textarea>
                <div class="form-text">Opcional. Define las reglas que deben seguir los participantes.</div>
                @error('rules')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="check_in_minutes">Tiempo de Check-in (minutos)</label>
                <input type="number"
                       id="check_in_minutes"
                       name="check_in_minutes"
                       class="form-control @error('check_in_minutes') is-invalid @enderror"
                       value="{{ old('check_in_minutes', $tournament->check_in_minutes ?? 15) }}"
                       min="15"
                       max="120"
                       required>
                <div class="form-hint">
                    <span class="hint-icon" title="Los capitanes deben confirmar su asistencia antes de cada partida">ℹ️</span>
                    Mínimo 15 minutos. Los equipos deben hacer check-in antes de este tiempo previo a cada partida.
                </div>
                @error('check_in_minutes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="start_date">Fecha inicio</label>
                <input type="date"
                       id="start_date"
                       name="start_date"
                       class="form-control @error('start_date') is-invalid @enderror"
                       value="{{ old('start_date', $tournament->start_date) }}"
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
                       value="{{ old('end_date', $tournament->end_date) }}"
                       required>
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Sección de Premios --}}
            <div class="form-group">
                <label>Premios por Posición</label>
                <div class="prizes-form-container">
                    <div class="prizes-form-header">
                        <h4>🏆 Configurar Premios</h4>
                    </div>
                    <div id="prizesContainer">
                        @php
                            $defaultPrizes = \App\Models\Tournament::getDefaultPrizes();
                            $currentPrizes = old('prizes', $tournament->prizes ?? $defaultPrizes);
                        @endphp
                        @foreach($currentPrizes as $index => $prize)
                            <div class="prize-entry" data-index="{{ $index }}">
                                <span class="prize-entry-position">
                                    @if($index === 0) 🥇
                                    @elseif($index === 1) 🥈
                                    @elseif($index === 2) 🥉
                                    @else 🏅
                                    @endif
                                </span>
                                <div class="prize-entry-fields">
                                    <input type="text"
                                           name="prizes[{{ $index }}][name]"
                                           placeholder="Nombre del premio (ej: Oro, Campeón...)"
                                           value="{{ $prize['name'] ?? '' }}"
                                           required>
                                    <input type="text"
                                           name="prizes[{{ $index }}][description]"
                                           placeholder="Descripción (ej: Trofeo de campeón)"
                                           value="{{ $prize['description'] ?? '' }}">
                                </div>
                                @if($index > 0)
                                    <button type="button" class="prize-entry-remove" onclick="removePrize(this)">✕</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm btn-add-prize" onclick="addPrize()">
                        + Añadir posición premiada
                    </button>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    let prizeIndex = {{ count($currentPrizes) }};
    const medals = ['🥇', '🥈', '🥉', '🏅', '🏅', '🏅', '🏅', '🏅', '🏅', '🏅'];

    // Preview del banner
    document.getElementById('banner').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('banner-preview-img').src = e.target.result;
                document.getElementById('banner-preview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    function clearBannerPreview() {
        document.getElementById('banner').value = '';
        document.getElementById('banner-preview').style.display = 'none';
        document.getElementById('banner-preview-img').src = '';
    }

    function addPrize() {
        const container = document.getElementById('prizesContainer');
        const entry = document.createElement('div');
        entry.className = 'prize-entry';
        entry.dataset.index = prizeIndex;

        entry.innerHTML = `
            <span class="prize-entry-position">${medals[prizeIndex] || '🏅'}</span>
            <div class="prize-entry-fields">
                <input type="text"
                       name="prizes[${prizeIndex}][name]"
                       placeholder="Nombre del premio (ej: 4º Puesto...)"
                       required>
                <input type="text"
                       name="prizes[${prizeIndex}][description]"
                       placeholder="Descripción del premio">
            </div>
            <button type="button" class="prize-entry-remove" onclick="removePrize(this)">✕</button>
        `;

        container.appendChild(entry);
        prizeIndex++;
    }

    function removePrize(button) {
        const entry = button.closest('.prize-entry');
        entry.remove();
        updatePrizeIndexes();
    }

    function updatePrizeIndexes() {
        const entries = document.querySelectorAll('#prizesContainer .prize-entry');
        entries.forEach((entry, index) => {
            entry.dataset.index = index;
            entry.querySelector('.prize-entry-position').textContent = medals[index] || '🏅';

            const inputs = entry.querySelectorAll('input');
            inputs[0].name = `prizes[${index}][name]`;
            inputs[1].name = `prizes[${index}][description]`;
        });
        prizeIndex = entries.length;
    }
</script>
@endpush
