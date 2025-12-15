@extends('layouts.app')

@section('content')
<h1>Equipo: {{ $team->name }}</h1>

<p>{{ $team->description }}</p>

<hr>

<h2>Jugadores</h2>

@if ($users->isEmpty())
    <p>Este equipo no tiene jugadores.</p>
@else
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }} ({{ $user->email }})</li>
        @endforeach
    </ul>
@endif

<hr>

<a href="{{ route('torneos.show', $team->tournament) }}">Volver al torneo</a>
@endsection
