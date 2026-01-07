@extends('layouts.app')

@section('title', $team->name)

@section('content')
    <div class="page-header">
        <div class="team-header-info">
            @if($team->logo)
                <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="team-header-logo">
            @else
                <div class="team-header-placeholder">
                    {{ strtoupper(substr($team->name, 0, 2)) }}
                </div>
            @endif
            <h1>{{ $team->name }}</h1>
        </div>
        <div class="actions-inline">
            <a href="{{ route('head-to-head.rivals', $team) }}" class="btn btn-primary">⚔️ Ver rivales</a>
            <a href="{{ route('torneos.show', $team->tournament_id) }}" class="btn btn-secondary">Volver al torneo</a>
        </div>
    </div>

    <div class="card">
        @if($team->description)
            <p>{{ $team->description }}</p>
        @endif

        @auth
            @php
                $user = auth()->user();
                $isMember = $team->users->contains('id', $user->id);
                $hasPendingRequest = \App\Models\TeamRequest::where('team_id', $team->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->exists();
                $hasTeamInTournament = $user->hasTeamInTournament($team->tournament);
            @endphp

            <p>
                <strong>Tu estado:</strong>
                @if ($isCaptain)
                    <span class="badge badge-primary">Gestor (Admin / Capitán)</span>
                @elseif ($isMember)
                    <span class="badge badge-success">Miembro del equipo</span>
                @elseif ($hasPendingRequest)
                    <span class="badge badge-warning">Solicitud pendiente</span>
                @else
                    <span class="text-muted">No eres miembro</span>
                @endif
            </p>

            {{-- Botón solicitar unirse --}}
            @if (!$isMember && !$hasPendingRequest && !$hasTeamInTournament && !$user->canManageTournaments())
                <form action="{{ route('teams.requestJoin', $team) }}" method="POST" class="mt-1">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="message" class="form-control" placeholder="Mensaje opcional (ej: Soy jugador de LOL Gold 3)" style="max-width: 400px;">
                    </div>
                    <button type="submit" class="btn btn-success">Solicitar unirme a este equipo</button>
                </form>
            @endif

            {{-- Botón cancelar solicitud --}}
            @if ($hasPendingRequest)
                <form action="{{ route('teams.cancelRequest', $team) }}" method="POST" class="mt-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary">Cancelar mi solicitud</button>
                </form>
            @endif

            {{-- Mensaje si ya tiene equipo en el torneo --}}
            @if (!$isMember && $hasTeamInTournament)
                <p class="text-muted mt-1">Ya perteneces a otro equipo en este torneo.</p>
            @endif
        @else
            <p><a href="{{ route('login') }}">Inicia sesión</a> para solicitar unirte a este equipo.</p>
        @endauth
    </div>

    {{-- Botones de gestión (solo capitán/admin) --}}
    @if ($isCaptain)
        <div class="actions mt-1">
            <a href="{{ route('invitations.create', $team) }}" class="btn btn-success">
                ✉️ Invitar jugadores
            </a>
            <a href="{{ route('teams.requests', $team) }}" class="btn btn-primary">
                Ver solicitudes pendientes ({{ $team->pendingRequests->count() }})
            </a>
        </div>
    @endif

    <div class="page-header">
        <h2>Jugadores ({{ $team->users->count() }})</h2>
    </div>

    @if ($team->users->isEmpty())
        <p class="text-muted">Este equipo no tiene jugadores.</p>
    @else
        @php
            $gamePositions = $team->tournament->game->positions ?? [];
            $currentUserId = auth()->id();
        @endphp

        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    @if(count($gamePositions) > 0)
                        <th>Posición</th>
                    @endif
                    @if ($isCaptain)
                        <th>Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($team->users as $member)
                    <tr>
                        <td><a href="{{ route('users.show', $member) }}">{{ $member->name }}</a></td>
                        <td>{{ $member->email }}</td>
                        <td>
                            @if ($member->pivot->role === 'captain')
                                <span class="badge badge-primary">Capitán</span>
                            @else
                                <span class="badge badge-success">Jugador</span>
                            @endif
                        </td>
                        @if(count($gamePositions) > 0)
                            <td>
                                @if($currentUserId === $member->id)
                                    {{-- El jugador puede editar sus propios roles --}}
                                    <form action="{{ route('teams.updateRoles', $team) }}" method="POST" class="role-selector-form">
                                        @csrf
                                        <div class="role-selectors">
                                            <select name="primary_role" class="form-control form-control-sm" title="Rol principal">
                                                <option value="">Principal...</option>
                                                @foreach($gamePositions as $position)
                                                    <option value="{{ $position }}" {{ $member->pivot->primary_role === $position ? 'selected' : '' }}>
                                                        {{ $position }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <select name="secondary_role" class="form-control form-control-sm" title="Rol secundario">
                                                <option value="">Secundario...</option>
                                                @foreach($gamePositions as $position)
                                                    <option value="{{ $position }}" {{ $member->pivot->secondary_role === $position ? 'selected' : '' }}>
                                                        {{ $position }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary" title="Guardar">✓</button>
                                        </div>
                                    </form>
                                @elseif($isCaptain)
                                    {{-- Capitán/Admin puede editar roles de otros --}}
                                    <form action="{{ route('teams.updateRoles', $team) }}" method="POST" class="role-selector-form">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                        <div class="role-selectors">
                                            <select name="primary_role" class="form-control form-control-sm" title="Rol principal">
                                                <option value="">Principal...</option>
                                                @foreach($gamePositions as $position)
                                                    <option value="{{ $position }}" {{ $member->pivot->primary_role === $position ? 'selected' : '' }}>
                                                        {{ $position }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <select name="secondary_role" class="form-control form-control-sm" title="Rol secundario">
                                                <option value="">Secundario...</option>
                                                @foreach($gamePositions as $position)
                                                    <option value="{{ $position }}" {{ $member->pivot->secondary_role === $position ? 'selected' : '' }}>
                                                        {{ $position }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary" title="Guardar">✓</button>
                                        </div>
                                    </form>
                                @else
                                    {{-- Mostrar roles del jugador (solo lectura) --}}
                                    <div class="player-roles">
                                        @if($member->pivot->primary_role)
                                            <span class="badge badge-info" title="Rol principal">{{ $member->pivot->primary_role }}</span>
                                        @endif
                                        @if($member->pivot->secondary_role)
                                            <span class="badge badge-secondary" title="Rol secundario">{{ $member->pivot->secondary_role }}</span>
                                        @endif
                                        @if(!$member->pivot->primary_role && !$member->pivot->secondary_role)
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endif
                        @if ($isCaptain)
                            <td class="actions-inline">
                                @if ($member->pivot->role !== 'captain')
                                    <form action="{{ route('teams.makeCaptain', [$team, $member]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Hacer capitán</button>
                                    </form>
                                @endif
                                <form action="{{ route('teams.users.remove', [$team, $member]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Quitar a {{ $member->name }} del equipo?')">Quitar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif

    @if ($isCaptain)
        <hr>

        <div class="card">
            <div class="card-header">Añadir jugador directamente</div>

            @if ($availableUsers->isEmpty())
                <p class="text-muted">No hay jugadores disponibles para añadir.</p>
            @else
                <form action="{{ route('teams.users.add', $team) }}" method="POST" class="actions-inline">
                    @csrf
                    <select name="user_id" class="form-control" style="width: auto; display: inline-block; min-width: 250px;" required>
                        <option value="">-- Selecciona jugador --</option>
                        @foreach ($availableUsers as $availableUser)
                            <option value="{{ $availableUser->id }}">{{ $availableUser->name }} ({{ $availableUser->email }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-success">Añadir</button>
                </form>
            @endif
        </div>
    @endif
@endsection
