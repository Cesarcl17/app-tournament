@extends('layouts.app')

@section('title', 'Torneos')

@section('content')
    <div class="page-header">
        <h2>Torneos</h2>
        @if(auth()->check() && auth()->user()->canManageTournaments())
            <a href="{{ route('torneos.create', $currentGame ? ['game' => $currentGame] : []) }}" class="btn btn-primary">
                Crear torneo{{ $currentGame ? ' de ' . $games->firstWhere('slug', $currentGame)?->short_name : '' }}
            </a>
        @endif
    </div>

    {{-- Filtros por juego --}}
    <div class="filters-bar mb-2">
        <a href="{{ route('torneos.index') }}" class="filter-chip {{ !$currentGame ? 'active' : '' }}">
            Todos
        </a>
        @foreach($games as $game)
            <a href="{{ route('torneos.index', ['game' => $game->slug]) }}"
               class="filter-chip {{ $currentGame === $game->slug ? 'active' : '' }}">
                {{ $game->short_name }}
            </a>
        @endforeach
    </div>

    @if($tournaments->isEmpty())
        <p class="text-muted">No hay torneos creados todavía.</p>
    @else
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Juego</th>
                    <th>Formato</th>
                    <th>Nombre</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tournaments as $tournament)
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
    @endif
@endsection
