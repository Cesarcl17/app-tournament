@extends('layouts.app')

@section('title', 'Jugadores inscritos - ' . $tournament->name)

@section('content')
    <div class="page-header">
        <h1>Jugadores inscritos</h1>
        <a href="{{ route('torneos.show', $tournament) }}" class="btn btn-secondary">Volver al torneo</a>
    </div>

    <div class="card">
        <div class="card-header">{{ $tournament->name }}</div>
        <p class="text-muted">
            Aquí puedes ver los jugadores que se han inscrito sin equipo y asignarlos a los equipos del torneo.
        </p>
    </div>

    <h2>Jugadores sin equipo ({{ $registeredUsers->count() }})</h2>

    @if($registeredUsers->isEmpty())
        <p class="text-muted">No hay jugadores inscritos sin equipo.</p>
    @else
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol global</th>
                    <th>Fecha inscripción</th>
                    <th>Asignar a equipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registeredUsers as $player)
                    <tr>
                        <td>{{ $player->name }}</td>
                        <td>{{ $player->email }}</td>
                        <td>
                            <span class="badge badge-{{ $player->isCaptain() ? 'primary' : 'success' }}">
                                {{ $player->role }}
                            </span>
                        </td>
                        <td>{{ $player->pivot->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($teams->isEmpty())
                                <span class="text-muted">No hay equipos</span>
                            @else
                                <form action="{{ route('torneos.assignPlayer', $tournament) }}" method="POST" class="actions-inline">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $player->id }}">
                                    <select name="team_id" class="form-control" style="width: auto; min-width: 150px;" required>
                                        <option value="">-- Equipo --</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Asignar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif

    <hr>

    <h2>Equipos del torneo ({{ $teams->count() }})</h2>

    @if($teams->isEmpty())
        <p class="text-muted">No hay equipos creados en este torneo.</p>
        <a href="{{ route('teams.create', $tournament) }}" class="btn btn-success">Crear equipo</a>
    @else
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Jugadores</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teams as $team)
                    <tr>
                        <td>
                            <a href="{{ route('teams.show', $team) }}">{{ $team->name }}</a>
                        </td>
                        <td>{{ $team->users->count() }} jugadores</td>
                        <td>
                            <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-secondary">Ver equipo</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
@endsection
