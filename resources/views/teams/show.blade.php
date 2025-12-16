@extends('layouts.app')

@section('content')
    <h1>{{ $team->name }}</h1>

    <p>{{ $team->description }}</p>

    <p>
        Rol en este equipo:
        <strong>
            @if ($isCaptain)
                Gestor (Admin / Capitán)
            @else
                Jugador
            @endif
        </strong>
    </p>

    <hr>

    <h2>Jugadores</h2>

    @if ($team->users->isEmpty())
        <p>Este equipo no tiene jugadores.</p>
    @else
        <ul>
            @foreach ($team->users as $user)
                <li>
                {{ $user->name }} ({{ $user->email }})

                @if ($user->pivot->role === 'captain')
                    — <strong>Capitán</strong>
                @else
                    — Jugador
                @endif

                @if ($isCaptain)
                    {{-- Acciones de gestión --}}
                    <form action="{{ route('teams.users.remove', [$team, $user]) }}"
                        method="POST"
                        style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('¿Quitar jugador del equipo?')">
                            Quitar
                        </button>
                    </form>

                    @if ($user->pivot->role !== 'captain')
                        <form action="{{ route('teams.makeCaptain', [$team, $user]) }}"
                            method="POST"
                            style="display:inline">
                            @csrf
                            <button type="submit">Hacer capitán</button>
                        </form>
                    @endif
                @endif
            </li>

            @endforeach
        </ul>
    @endif

    <hr>

    @if ($isCaptain)
        <h3>Añadir jugador</h3>

        @if ($availableUsers->isEmpty())
            <p>No hay jugadores disponibles para añadir.</p>
        @else
            <form action="{{ route('teams.users.add', $team) }}" method="POST">
                @csrf

                <select name="user_id" required>
                    <option value="">-- Selecciona jugador --</option>
                    @foreach ($availableUsers as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>

                <button type="submit">Añadir</button>
            </form>
        @endif
    @endif

    <hr>

    <a href="{{ route('torneos.show', $team->tournament_id) }}">
        Volver al torneo
    </a>
@endsection
