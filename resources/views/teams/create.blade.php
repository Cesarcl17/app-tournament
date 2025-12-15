@extends('layouts.app')

@section('content')
<h1>Crear equipo para el torneo: {{ $tournament->name }}</h1>

<form method="POST" action="{{ route('teams.store', $tournament) }}">
    @csrf

    <div>
        <label>Nombre del equipo</label><br>
        <input type="text" name="name" required>
    </div>

    <br>

    <div>
        <label>Descripción</label><br>
        <textarea name="description"></textarea>
    </div>

    <br>

    <button type="submit">Crear equipo</button>
</form>

<br>
<a href="{{ route('torneos.show', $tournament) }}">Volver al torneo</a>
@endsection
