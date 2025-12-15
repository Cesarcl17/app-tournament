@extends('layouts.app')

@section('content')
    <h1>{{ $tournament->name }}</h1>

    <p>{{ $tournament->description }}</p>

    <p>
        <strong>Inicio:</strong> {{ $tournament->start_date }} <br>
        <strong>Fin:</strong> {{ $tournament->end_date }}
    </p>

    <hr>

    <h2>Equipos</h2>

    @if ($teams->isEmpty())
        <p>Este torneo no tiene equipos todavía.</p>
    @else
        <ul>
            @foreach ($teams as $team)
                <li>
                    <a href="{{ route('teams.show', $team) }}">
                        {{ $team->name }}
                    </a>

                    <a href="{{ route('teams.edit', $team) }}">Editar</a>

                    <form action="{{ route('teams.destroy', $team) }}"
                        method="POST"
                        style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('¿Eliminar este equipo?')">
                            Eliminar
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif




    <hr>

    <a href="{{ route('torneos.index') }}">Volver</a>
    <br><br>

    <a href="{{ route('torneos.edit', $tournament) }}">Editar torneo</a>

    <form action="{{ route('torneos.destroy', $tournament) }}" method="POST" style="margin-top: 10px;">
        @csrf
        @method('DELETE')

        <button type="submit" onclick="return confirm('¿Seguro que quieres eliminar este torneo?')">
            Eliminar torneo
        </button>
    </form>
@endsection
