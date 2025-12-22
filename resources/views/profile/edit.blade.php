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
@endsection
