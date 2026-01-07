@extends('layouts.app')

@section('title', 'Invitación expirada')

@section('content')
    <div class="invitation-container">
        <div class="invitation-card expired">
            <div class="invitation-header">
                <span class="invitation-icon">⏰</span>
                <h1>Invitación expirada</h1>
            </div>

            <div class="invitation-details">
                <p>Esta invitación al equipo <strong>{{ $invitation->team->name }}</strong> ha expirado.</p>
                <p>La invitación expiró el {{ $invitation->expires_at->format('d/m/Y \a \l\a\s H:i') }}.</p>
            </div>

            <div class="invitation-actions">
                <p class="text-muted">
                    Si aún deseas unirte al equipo, puedes solicitar unirte directamente o pedir que te envíen una nueva invitación.
                </p>
                <a href="{{ route('teams.show', $invitation->team) }}" class="btn btn-primary">
                    Ver equipo
                </a>
                <a href="{{ route('home') }}" class="btn btn-secondary">
                    Ir al inicio
                </a>
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

    .invitation-card.expired .invitation-header {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    .invitation-header {
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .invitation-icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .invitation-details {
        padding: 2rem;
        text-align: center;
    }

    .invitation-actions {
        padding: 1.5rem 2rem 2rem;
        text-align: center;
        border-top: 1px solid #eee;
    }

    .invitation-actions .btn {
        margin: 0.25rem;
    }
</style>
@endpush
