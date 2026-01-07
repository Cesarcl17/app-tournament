@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div class="page-header">
        <h1>Mi Perfil</h1>
    </div>

    <div class="card">
        <div class="card-header">Información de la cuenta</div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">Rol</label>
                <select id="role"
                        name="role"
                        class="form-control @error('role') is-invalid @enderror"
                        @if($user->isAdmin() || $user->isOrganizer()) disabled @endif>
                    @if($user->isAdmin())
                        <option value="admin" selected>Administrador</option>
                    @elseif($user->isOrganizer())
                        <option value="organizer" selected>Organizador</option>
                    @else
                        @foreach(App\Models\User::availableRoles() as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @if($user->isAdmin() || $user->isOrganizer())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <small class="text-muted">Tu rol de {{ $user->role }} no se puede cambiar.</small>
                @else
                    <small class="text-muted">
                        <strong>Jugador:</strong> Puedes unirte a equipos.<br>
                        <strong>Capitán:</strong> Puedes gestionar tu propio equipo.
                    </small>
                @endif
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <div class="card-header mb-1">Cambiar contraseña <small class="text-muted">(opcional)</small></div>

            <div class="form-group">
                <label for="current_password">Contraseña actual</label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       class="form-control @error('current_password') is-invalid @enderror">
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nueva contraseña</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar nueva contraseña</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control">
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('torneos.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

    <hr>

    <div class="card">
        <div class="card-header">Mis equipos</div>

        @if($user->teams->isEmpty())
            <p class="text-muted">No perteneces a ningún equipo todavía.</p>
        @else
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Torneo</th>
                        <th>Mi rol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->teams as $team)
                        <tr>
                            <td>
                                <a href="{{ route('teams.show', $team) }}">{{ $team->name }}</a>
                            </td>
                            <td>
                                <a href="{{ route('torneos.show', $team->tournament) }}">
                                    {{ $team->tournament->name }}
                                </a>
                            </td>
                            <td>
                                @if($team->pivot->role === 'captain')
                                    <span class="badge badge-primary">Capitán</span>
                                @else
                                    <span class="badge badge-success">Jugador</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>

    {{-- Sección de Estadísticas --}}
    <div class="card mt-2">
        <div class="card-header">📊 Mis Estadísticas</div>

        @php
            $stats = $user->statistics;
        @endphp

        @if($stats)
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats->matches_played }}</div>
                    <div class="stat-label">Partidas Jugadas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value text-success">{{ $stats->wins }}</div>
                    <div class="stat-label">Victorias</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value text-danger">{{ $stats->losses }}</div>
                    <div class="stat-label">Derrotas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats->win_rate }}%</div>
                    <div class="stat-label">Win Rate</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats->tournaments_played }}</div>
                    <div class="stat-label">Torneos Jugados</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value text-warning">🏆 {{ $stats->tournaments_won }}</div>
                    <div class="stat-label">Torneos Ganados</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">🔥 {{ $stats->current_win_streak }}</div>
                    <div class="stat-label">Racha Actual</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">⭐ {{ $stats->best_win_streak }}</div>
                    <div class="stat-label">Mejor Racha</div>
                </div>
            </div>
        @else
            <p class="text-muted p-1">Aún no tienes estadísticas. ¡Participa en un torneo para empezar!</p>
        @endif
    </div>

    {{-- Sección de Trofeos --}}
    <div class="card mt-2">
        <div class="card-header">🏆 Mis Trofeos</div>

        @if($user->trophies->isEmpty())
            <p class="text-muted p-1">Aún no tienes trofeos. ¡Gana un torneo para obtener tu primer trofeo!</p>
        @else
            <div class="trophies-grid">
                @foreach($user->trophies as $trophy)
                    <div class="trophy-item">
                        <div class="trophy-icon">
                            @if($trophy->game && $trophy->game->logo)
                                <img src="{{ asset('images/games/' . $trophy->game->logo) }}" 
                                     alt="{{ $trophy->game->name }}" 
                                     class="trophy-game-logo">
                            @else
                                🏆
                            @endif
                        </div>
                        <div class="trophy-info">
                            <div class="trophy-name">{{ $trophy->name }}</div>
                            <div class="trophy-description">{{ $trophy->description }}</div>
                            <div class="trophy-date">
                                <small class="text-muted">
                                    Obtenido: {{ \Carbon\Carbon::parse($trophy->pivot->earned_at)->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        background: var(--bg-secondary, #f8f9fa);
        border-radius: 8px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: bold;
        color: var(--text-primary, #333);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted, #666);
        margin-top: 0.25rem;
    }

    .text-success { color: #28a745; }
    .text-danger { color: #dc3545; }
    .text-warning { color: #ffc107; }

    .trophies-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    .trophy-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
        border: 2px solid #ffc107;
        border-radius: 12px;
        transition: transform 0.2s;
    }

    .trophy-item:hover {
        transform: translateY(-2px);
    }

    .trophy-icon {
        font-size: 2.5rem;
        min-width: 60px;
        text-align: center;
    }

    .trophy-game-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
        border-radius: 8px;
    }

    .trophy-info {
        flex: 1;
    }

    .trophy-name {
        font-weight: bold;
        font-size: 1rem;
        color: #333;
    }

    .trophy-description {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.25rem;
    }

    .trophy-date {
        margin-top: 0.5rem;
    }

    .mt-2 {
        margin-top: 1.5rem;
    }

    .p-1 {
        padding: 1rem;
    }
</style>
@endpush
