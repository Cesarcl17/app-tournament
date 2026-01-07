@extends('layouts.app')

@section('title', $tournament->name)

@section('content')
    {{-- Banner del torneo --}}
    @if($tournament->banner)
        <div class="tournament-banner">
            <img src="{{ asset('storage/' . $tournament->banner) }}" alt="{{ $tournament->name }}">
            <div class="tournament-banner-overlay">
                <h1>{{ $tournament->name }}</h1>
            </div>
        </div>
    @endif

    <div class="page-header">
        @if(!$tournament->banner)
            <h1>{{ $tournament->name }}</h1>
        @else
            <div></div>
        @endif
        <a href="{{ route('torneos.index') }}" class="btn btn-secondary">Volver a torneos</a>
    </div>

    <div class="card">
        @if($tournament->game)
            <p>
                <span class="badge badge-primary">{{ $tournament->game->name }}</span>
                <span class="badge badge-success">{{ $tournament->getFormatLabel() }}</span>
                <span class="badge badge-secondary">⏱️ Check-in: {{ $tournament->check_in_minutes ?? 15 }} min</span>
            </p>
        @endif

        @if($tournament->description)
            <p>{{ $tournament->description }}</p>
        @endif
        <p>
            <strong>Fecha inicio:</strong> {{ $tournament->start_date }}<br>
            <strong>Fecha fin:</strong> {{ $tournament->end_date }}
        </p>

        {{-- Sección de Reglas --}}
        @if($tournament->rules)
            <div class="tournament-section" id="rulesSection">
                <div class="tournament-section-header" onclick="toggleSection('rulesSection')">
                    <h3>📋 Reglas del Torneo</h3>
                    <span class="tournament-section-toggle">▼</span>
                </div>
                <div class="tournament-section-content">
                    <div class="rules-content">{{ $tournament->rules }}</div>
                </div>
            </div>
        @endif

        {{-- Sección de Premios --}}
        @if($tournament->prizes && count($tournament->prizes) > 0)
            <div class="tournament-section" id="prizesSection">
                <div class="tournament-section-header" onclick="toggleSection('prizesSection')">
                    <h3>🏆 Premios</h3>
                    <span class="tournament-section-toggle">▼</span>
                </div>
                <div class="tournament-section-content">
                    <div class="prizes-list">
                        @foreach($tournament->getPrizesForDisplay() as $prize)
                            <div class="prize-item">
                                <span class="prize-medal">{{ $prize['medal'] }}</span>
                                <div class="prize-info">
                                    <span class="prize-position">{{ $prize['position'] }}º Puesto</span>
                                    <span class="prize-name">{{ $prize['name'] }}</span>
                                    @if($prize['description'])
                                        <span class="prize-description">{{ $prize['description'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

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

        {{-- Sección del Bracket --}}
        <div class="bracket-actions mt-2">
            @if($tournament->hasBracket())
                <a href="{{ route('torneos.bracket', $tournament) }}" class="btn btn-success">
                    🏆 Ver Bracket
                </a>
                @if($tournament->getChampion())
                    <span class="badge badge-success">Campeón: {{ $tournament->getChampion()->name }}</span>
                @endif
            @else
                @auth
                    @if(auth()->user()->canManageTournaments())
                        @if($tournament->canGenerateBracket())
                            <form action="{{ route('torneos.generateBracket', $tournament) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('¿Generar el bracket con {{ $tournament->teams->count() }} equipos? Los emparejamientos serán aleatorios.')">
                                    🎲 Generar Bracket
                                </button>
                            </form>
                        @elseif($tournament->teams->count() < \App\Models\Tournament::MIN_TEAMS_FOR_BRACKET)
                            <span class="badge badge-warning">
                                Se necesitan mínimo {{ \App\Models\Tournament::MIN_TEAMS_FOR_BRACKET }} equipos para generar el bracket
                                (actual: {{ $tournament->teams->count() }})
                            </span>
                        @endif
                    @endif
                @endauth
            @endif
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

@push('scripts')
<script>
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.classList.toggle('collapsed');
    }
</script>
@endpush
