@extends('layouts.app')

@section('title', 'Rivales de ' . $team->name)

@section('content')
    <div class="page-header">
        <h1>⚔️ Rivales de {{ $team->name }}</h1>
        <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">Volver al equipo</a>
    </div>

    @if($rivals->isEmpty())
        <div class="empty-state">
            <p class="text-muted">Este equipo aún no tiene historial de enfrentamientos.</p>
        </div>
    @else
        <div class="rivals-list">
            @foreach($rivals as $rivalData)
                @php
                    $rival = $rivalData['team'];
                    $wins = $rivalData['wins'];
                    $losses = $rivalData['losses'];
                    $total = $rivalData['total'];
                    $winRate = $total > 0 ? round(($wins / $total) * 100) : 0;
                @endphp
                <div class="rival-card">
                    <div class="rival-info">
                        <a href="{{ route('teams.show', $rival) }}" class="rival-name">
                            {{ $rival->name }}
                        </a>
                        @if($rival->tournament)
                            <span class="rival-tournament">{{ $rival->tournament->name }}</span>
                        @endif
                    </div>

                    <div class="rival-stats">
                        <div class="stat">
                            <span class="value text-success">{{ $wins }}</span>
                            <span class="label">Victorias</span>
                        </div>
                        <div class="stat">
                            <span class="value text-danger">{{ $losses }}</span>
                            <span class="label">Derrotas</span>
                        </div>
                        <div class="stat">
                            <span class="value">{{ $total }}</span>
                            <span class="label">Total</span>
                        </div>
                        <div class="stat">
                            <span class="value {{ $winRate >= 50 ? 'text-success' : 'text-danger' }}">{{ $winRate }}%</span>
                            <span class="label">Win Rate</span>
                        </div>
                    </div>

                    <div class="rival-actions">
                        <a href="{{ route('head-to-head.show', [$team, $rival]) }}" class="btn btn-sm btn-primary">
                            Ver historial
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('styles')
<style>
    /* Empty state usa estilos globales */

    .rivals-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .rival-card {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 2rem;
        align-items: center;
        padding: 1.25rem;
        background: var(--bg-secondary, #f8f9fa);
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .rival-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .rival-name {
        font-size: 1.1rem;
        font-weight: bold;
        color: #007bff;
        text-decoration: none;
    }

    .rival-name:hover {
        text-decoration: underline;
    }

    .rival-tournament {
        font-size: 0.9rem;
        color: #666;
    }

    .rival-stats {
        display: flex;
        gap: 1.5rem;
    }

    .rival-stats .stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 60px;
    }

    .rival-stats .value {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .rival-stats .label {
        font-size: 0.75rem;
        color: #666;
    }

    .text-success { color: #28a745; }
    .text-danger { color: #dc3545; }

    @media (max-width: 768px) {
        .rival-card {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .rival-stats {
            justify-content: space-between;
        }

        .rival-actions {
            text-align: center;
        }
    }
</style>
@endpush
