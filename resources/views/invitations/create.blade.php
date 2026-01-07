@extends('layouts.app')

@section('title', 'Invitar jugadores - ' . $team->name)

@section('content')
    <div class="page-header">
        <h1>✉️ Invitar jugadores a {{ $team->name }}</h1>
        <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">Volver al equipo</a>
    </div>

    <div class="card">
        <div class="card-header">Enviar invitación por email</div>

        <form action="{{ route('invitations.store', $team) }}" method="POST" class="invitation-form">
            @csrf

            <div class="form-group">
                <label for="email">Email del jugador *</label>
                <input type="email"
                       name="email"
                       id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="jugador@ejemplo.com"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Si el usuario no tiene cuenta, recibirá un enlace para registrarse.
                </small>
            </div>

            <div class="form-group">
                <label for="message">Mensaje personalizado (opcional)</label>
                <textarea name="message"
                          id="message"
                          class="form-control @error('message') is-invalid @enderror"
                          rows="3"
                          placeholder="¡Únete a nuestro equipo! Necesitamos un jugador más..."
                          maxlength="500">{{ old('message') }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                📧 Enviar invitación
            </button>
        </form>
    </div>

    {{-- Invitaciones pendientes --}}
    @if($pendingInvitations->isNotEmpty())
        <div class="card mt-2">
            <div class="card-header">Invitaciones pendientes</div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Enviada</th>
                        <th>Expira</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingInvitations as $invitation)
                        <tr>
                            <td>{{ $invitation->email }}</td>
                            <td>{{ $invitation->created_at->diffForHumans() }}</td>
                            <td>{{ $invitation->expires_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('invitations.destroy', $invitation) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar esta invitación?')">
                                        Cancelar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

@push('styles')
<style>
    .invitation-form {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .form-text {
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .mt-2 { margin-top: 1rem; }
    .inline { display: inline; }
</style>
@endpush
