@extends('layouts.app')

@section('title', $tournament->name)

@section('content')
    <div class="page-header">
        <h1>{{ $tournament->name }}</h1>
        <a href="{{ route('torneos.index') }}" class="btn btn-secondary">Volver a torneos</a>
    </div>

    <div class="card">
        @if($tournament->game)
            <p>
                <span class="badge badge-primary">{{ $tournament->game->name }}</span>
                <span class="badge badge-success">{{ $tournament->getFormatLabel() }}</span>
            </p>
        @endif

        @if($tournament->description)
            <p>{{ $tournament->description }}</p>
        @endif
        <p>
            <strong>Fecha inicio:</strong> {{ $tournament->start_date }}<br>
            <strong>Fecha fin:</strong> {{ $tournament->end_date }}
        </p>

        {{-- Acciones según rol --}}
        <div class="actions-inline">
            @auth
                @php
                    $user = auth()->user();
                    $isRegistered = $user->isRegisteredInTournament($tournament);
                    $hasTeam = $user->hasTeamInTournament($tournament);
                @endphp

                {{-- Botón inscribirse (solo jugadores/capitanes sin equipo ni inscripción) --}}
                @if(!$user->canManageTournaments() && !$isRegistered && !$hasTeam)
                    <form action="{{ route('torneos.register', $tournament) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Inscribirme como jugador suelto</button>
                    </form>
                @endif

                {{-- Botón cancelar inscripción --}}
                @if($isRegistered && !$hasTeam)
                    <form action="{{ route('torneos.unregister', $tournament) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Cancelar tu inscripción?')">Cancelar inscripción</button>
                    </form>
                @endif

                {{-- Mensaje si ya tiene equipo --}}
                @if($hasTeam)
                    <span class="badge badge-success">Ya perteneces a un equipo en este torneo</span>
                @endif

                {{-- Acciones de organizador/admin --}}
                @if($user->canManageTournaments())
                    <a href="{{ route('torneos.players', $tournament) }}" class="btn btn-primary">
                        Ver jugadores inscritos ({{ $tournament->registeredUsers->count() }})
                    </a>
                    <a href="{{ route('torneos.edit', $tournament) }}" class="btn btn-secondary">Editar torneo</a>
                    <form action="{{ route('torneos.destroy', $tournament) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar este torneo y todos sus equipos?')">Eliminar torneo</button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Inicia sesión para inscribirte</a>
            @endauth
        </div>
    </div>

    <div class="page-header">
        <h2>Equipos</h2>
        @if(auth()->check() && auth()->user()->canManageTournaments())
            <a href="{{ route('teams.create', $tournament) }}" class="btn btn-success">Crear equipo</a>
        @endif
    </div>

    @if ($teams->isEmpty())
        <p class="text-muted">Este torneo no tiene equipos todavía.</p>
    @else
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Jugadores</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teams as $team)
                    <tr>
                        <td>
                            <a href="{{ route('teams.show', $team) }}">{{ $team->name }}</a>
                        </td>
                        <td>{{ $team->description ?? '-' }}</td>
                        <td>{{ $team->users->count() }}</td>
                        <td class="actions-inline">
                            <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-secondary">Ver</a>
                            @if(auth()->check() && auth()->user()->canManageTournaments())
                                <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-primary">Editar</a>
                                <form action="{{ route('teams.destroy', $team) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este equipo?')">Eliminar</button>
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
