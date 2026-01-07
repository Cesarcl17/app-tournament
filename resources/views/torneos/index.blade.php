@extends('layouts.app')

@section('title', 'Torneos')

@section('content')
    <div class="page-header">
        <h2>Torneos</h2>
        @if(auth()->check() && auth()->user()->canManageTournaments())
            <a href="{{ route('torneos.create', $filters['game'] ? ['game' => $filters['game']] : []) }}" class="btn btn-primary">
                Crear torneo{{ $filters['game'] ? ' de ' . $games->firstWhere('slug', $filters['game'])?->short_name : '' }}
            </a>
        @endif
    </div>

    {{-- Barra de búsqueda y filtros --}}
    <div class="search-filters-container mb-2">
        <form action="{{ route('torneos.index') }}" method="GET" class="search-form">
            {{-- Búsqueda por nombre --}}
            <div class="search-box">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Buscar torneo por nombre..." 
                       value="{{ $filters['search'] ?? '' }}">
                <button type="submit" class="btn btn-primary">🔍</button>
            </div>

            {{-- Filtros en fila --}}
            <div class="filters-row">
                {{-- Filtro por juego --}}
                <div class="filter-group">
                    <label>Juego:</label>
                    <select name="game" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach($games as $game)
                            <option value="{{ $game->slug }}" {{ ($filters['game'] ?? '') === $game->slug ? 'selected' : '' }}>
                                {{ $game->short_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por formato --}}
                <div class="filter-group">
                    <label>Formato:</label>
                    <select name="format" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="1" {{ ($filters['format'] ?? '') == '1' ? 'selected' : '' }}>1v1</option>
                        <option value="3" {{ ($filters['format'] ?? '') == '3' ? 'selected' : '' }}>3v3</option>
                        <option value="5" {{ ($filters['format'] ?? '') == '5' ? 'selected' : '' }}>5v5</option>
                    </select>
                </div>

                {{-- Filtro por estado --}}
                <div class="filter-group">
                    <label>Estado:</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="upcoming" {{ ($filters['status'] ?? '') === 'upcoming' ? 'selected' : '' }}>Próximos</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>En curso</option>
                        <option value="finished" {{ ($filters['status'] ?? '') === 'finished' ? 'selected' : '' }}>Finalizados</option>
                    </select>
                </div>

                {{-- Limpiar filtros --}}
                @if(array_filter($filters))
                    <a href="{{ route('torneos.index') }}" class="btn btn-secondary btn-sm">✕ Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Indicador de filtros activos --}}
    @if(array_filter($filters))
        <div class="active-filters mb-1">
            <span class="text-muted">Mostrando {{ $tournaments->count() }} de {{ $tournaments->total() }} resultado(s)</span>
        </div>
    @endif

    @if($tournaments->isEmpty())
        <div class="empty-state">
            <p class="text-muted">No se encontraron torneos con los filtros seleccionados.</p>
            @if(array_filter($filters))
                <a href="{{ route('torneos.index') }}" class="btn btn-primary">Ver todos los torneos</a>
            @endif
        </div>
    @else
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Juego</th>
                    <th>Formato</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tournaments as $tournament)
                    @php
                        $today = now()->toDateString();
                        $isUpcoming = $tournament->start_date > $today;
                        $isActive = $tournament->start_date <= $today && $tournament->end_date >= $today;
                        $isFinished = $tournament->end_date < $today;
                    @endphp
                    <tr>
                        <td>
                            @if($tournament->game)
                                <span class="badge badge-primary">{{ $tournament->game->short_name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-success">{{ $tournament->getFormatLabel() }}</span>
                        </td>
                        <td>
                            <a href="{{ route('torneos.show', $tournament) }}">
                                {{ $tournament->name }}
                            </a>
                        </td>
                        <td>
                            @if($isFinished)
                                <span class="badge badge-secondary">Finalizado</span>
                            @elseif($isActive)
                                <span class="badge badge-success">En curso</span>
                            @else
                                <span class="badge badge-warning">Próximo</span>
                            @endif
                        </td>
                        <td>{{ $tournament->start_date }}</td>
                        <td>{{ $tournament->end_date }}</td>
                        <td class="actions-inline">
                            <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-sm btn-secondary">Ver</a>
                            @if(auth()->check() && auth()->user()->canManageTournaments())
                                <a href="{{ route('torneos.edit', $tournament) }}" class="btn btn-sm btn-primary">Editar</a>
                                <form action="{{ route('torneos.destroy', $tournament) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este torneo?')">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        {{-- Paginación --}}
        @if($tournaments->hasPages())
            <div class="pagination-container mt-2">
                {{ $tournaments->links() }}
            </div>
        @endif
    @endif
@endsection

@push('styles')
<style>
    .search-filters-container {
        background: var(--bg-card);
        padding: 20px;
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .search-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .search-box {
        display: flex;
        gap: 10px;
    }

    .search-box input {
        flex: 1;
    }

    .filters-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-group label {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-group select {
        min-width: 140px;
    }

    .active-filters {
        font-size: 0.9rem;
    }

    /* Empty state usa estilos globales */

    @media (max-width: 768px) {
        .search-filters-container {
            padding: 16px;
            border-radius: 12px;
        }

        .filters-row {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
        }

        .filter-group select {
            width: 100%;
        }
    }
</style>
@endpush
