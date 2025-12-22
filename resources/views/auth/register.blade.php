@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
    <h1>Crear cuenta</h1>

    <div class="card">
        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       required
                       autofocus>
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
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       required>
            </div>

            <div class="form-group">
                <label for="role">¿Cómo quieres participar?</label>
                <select id="role"
                        name="role"
                        class="form-control @error('role') is-invalid @enderror">
                    @foreach(App\Models\User::availableRoles() as $value => $label)
                        <option value="{{ $value }}" {{ old('role', 'player') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                    <strong>Jugador:</strong> Puedes unirte a equipos existentes.<br>
                    <strong>Capitán:</strong> Puedes crear y gestionar tu propio equipo.
                </small>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Registrarse</button>
                <a href="{{ route('login') }}" class="btn btn-secondary">Ya tengo cuenta</a>
            </div>
        </form>
    </div>
@endsection
