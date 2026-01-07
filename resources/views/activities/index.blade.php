@extends('layouts.app')

@section('title', 'Actividad Reciente')

@section('content')
    <div class="page-header">
        <h1>📢 Actividad Reciente</h1>
    </div>

    {{-- Filtros --}}
    <div class="activity-filters mb-2">
        <a href="{{ route('activities.index') }}" 
           class="filter-chip {{ !$currentType ? 'active' : '' }}">
            Todas
        </a>
        @foreach($types as $type => $label)
            <a href="{{ route('activities.index', ['type' => $type]) }}" 
               class="filter-chip {{ $currentType === $type ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($activities->isEmpty())
        <div class="empty-state">
            <p class="text-muted">No hay actividad registrada todavía.</p>
        </div>
    @else
        <div class="activity-feed">
            @foreach($activities as $activity)
                <div class="activity-item">
                    <div class="activity-icon">
                        {{ $activity->icon }}
                    </div>
                    <div class="activity-content">
                        <div class="activity-description">
                            {{ $activity->description }}
                        </div>
                        <div class="activity-meta">
                            <span class="activity-time">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                            @if($activity->user)
                                <span class="activity-user">
                                    por <a href="{{ route('users.show', $activity->user) }}">{{ $activity->user->name }}</a>
                                </span>
                            @endif
                            @if($activity->subject)
                                @if($activity->subject_type === 'App\\Models\\Tournament')
                                    <a href="{{ route('torneos.show', $activity->subject) }}" class="activity-link">
                                        Ver torneo →
                                    </a>
                                @elseif($activity->subject_type === 'App\\Models\\Team')
                                    <a href="{{ route('teams.show', $activity->subject) }}" class="activity-link">
                                        Ver equipo →
                                    </a>
                                @elseif($activity->subject_type === 'App\\Models\\TournamentMatch')
                                    <a href="{{ route('torneos.bracket', $activity->subject->tournament) }}" class="activity-link">
                                        Ver bracket →
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="pagination-container mt-2">
            {{ $activities->links() }}
        </div>
    @endif
@endsection

@push('styles')
<style>
    .activity-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .filter-chip {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: var(--bg-secondary, #f8f9fa);
        border: 1px solid #ddd;
        border-radius: 20px;
        color: #666;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .filter-chip:hover {
        background: #e9ecef;
        border-color: #ccc;
    }

    .filter-chip.active {
        background: #007bff;
        border-color: #007bff;
        color: white;
    }

    .activity-feed {
        display: flex;
        flex-direction: column;
    }

    .activity-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid #eee;
        transition: background 0.2s;
    }

    .activity-item:hover {
        background: var(--bg-secondary, #f8f9fa);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-secondary, #f8f9fa);
        border-radius: 50%;
        font-size: 1.25rem;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-description {
        font-size: 1rem;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }

    .activity-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: #666;
    }

    .activity-time {
        color: #999;
    }

    .activity-user a {
        color: #007bff;
        text-decoration: none;
    }

    .activity-user a:hover {
        text-decoration: underline;
    }

    .activity-link {
        color: #007bff;
        text-decoration: none;
    }

    .activity-link:hover {
        text-decoration: underline;
    }

    /* Empty state usa estilos globales */

    .mb-2 { margin-bottom: 1rem; }
    .mt-2 { margin-top: 1rem; }
</style>
@endpush
