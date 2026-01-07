@extends('layouts.app')

@section('title', 'Invitación procesada')

@section('content')
    <div class="invitation-container">
        <div class="invitation-card processed">
            <div class="invitation-header">
                @if($invitation->status === 'accepted')
                    <span class="invitation-icon">✅</span>
                    <h1>Invitación aceptada</h1>
                @else
                    <span class="invitation-icon">❌</span>
                    <h1>Invitación rechazada</h1>
                @endif
            </div>

            <div class="invitation-details">
                @if($invitation->status === 'accepted')
                    <p>Esta invitación al equipo <strong>{{ $invitation->team->name }}</strong> ya fue aceptada.</p>
                    @if($invitation->accepted_at)
                        <p class="text-muted">Aceptada el {{ $invitation->accepted_at->format('d/m/Y \a \l\a\s H:i') }}.</p>
                    @endif
                @else
                    <p>Esta invitación al equipo <strong>{{ $invitation->team->name }}</strong> fue rechazada.</p>
                @endif
            </div>

            <div class="invitation-actions">
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

    .invitation-header {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .invitation-card.processed .invitation-header {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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
