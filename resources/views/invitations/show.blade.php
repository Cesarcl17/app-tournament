@extends('layouts.app')

@section('title', 'Invitación - ' . $invitation->team->name)

@section('content')
    <div class="invitation-container">
        <div class="invitation-card">
            <div class="invitation-header">
                <span class="invitation-icon">✉️</span>
                <h1>Invitación al equipo</h1>
            </div>

            <div class="invitation-details">
                <div class="team-info">
                    <h2>{{ $invitation->team->name }}</h2>
                    <p class="tournament-name">
                        🏆 {{ $invitation->team->tournament->name }}
                        @if($invitation->team->tournament->game)
                            <span class="badge badge-primary">{{ $invitation->team->tournament->game->short_name }}</span>
                        @endif
                    </p>
                </div>

                <div class="inviter-info">
                    <p>
                        Invitado por <strong>{{ $invitation->inviter->name }}</strong>
                    </p>
                    @if($invitation->message)
                        <div class="invitation-message">
                            <p>"{{ $invitation->message }}"</p>
                        </div>
                    @endif
                </div>

                <div class="invitation-expiry">
                    <small>Esta invitación expira el {{ $invitation->expires_at->format('d/m/Y \a \l\a\s H:i') }}</small>
                </div>
            </div>

            <div class="invitation-actions">
                @if($isLoggedIn)
                    @if($emailMatches)
                        <form action="{{ route('invitations.accept', $invitation->token) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                ✓ Aceptar invitación
                            </button>
                        </form>
                    @else
                        <div class="email-mismatch-warning">
                            <p class="text-warning">
                                ⚠️ Esta invitación fue enviada a <strong>{{ $invitation->email }}</strong>,
                                pero estás conectado como <strong>{{ auth()->user()->email }}</strong>.
                            </p>
                            <p>Si esta invitación es para ti, puedes aceptarla de todos modos:</p>
                            <form action="{{ route('invitations.accept', $invitation->token) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    ✓ Aceptar de todos modos
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="login-prompt">
                        <p>Para aceptar esta invitación, debes iniciar sesión o crear una cuenta.</p>
                        <div class="auth-buttons">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                Iniciar sesión
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                                Crear cuenta
                            </a>
                        </div>
                    </div>
                @endif

                <form action="{{ route('invitations.reject', $invitation->token) }}" method="POST" class="reject-form">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger" onclick="return confirm('¿Rechazar esta invitación?')">
                        Rechazar invitación
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .invitation-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
        padding: 2rem;
    }

    .invitation-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
        overflow: hidden;
    }

    .invitation-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .invitation-icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .invitation-header h1 {
        margin: 0;
        font-size: 1.5rem;
    }

    .invitation-details {
        padding: 2rem;
    }

    .team-info {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .team-info h2 {
        margin: 0 0 0.5rem 0;
        color: #1a1a2e;
    }

    .tournament-name {
        color: #666;
        margin: 0;
    }

    .inviter-info {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .invitation-message {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 1rem;
        margin-top: 1rem;
        border-radius: 0 4px 4px 0;
    }

    .invitation-message p {
        margin: 0;
        font-style: italic;
        color: #555;
    }

    .invitation-expiry {
        text-align: center;
        color: #999;
    }

    .invitation-actions {
        padding: 1.5rem 2rem 2rem;
        text-align: center;
        border-top: 1px solid #eee;
    }

    .email-mismatch-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .login-prompt {
        margin-bottom: 1rem;
    }

    .auth-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 1rem;
    }

    .reject-form {
        margin-top: 1rem;
    }

    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
    }

    .inline { display: inline; }
</style>
@endpush
