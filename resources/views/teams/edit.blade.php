@extends('layouts.app')

@section('content')
<h1>Editar equipo</h1>

<form method="POST" action="{{ route('teams.update', $team) }}">
    @csrf
    @method('PUT')

    <label>Nombre</label><br>
    <input type="text" name="name" value="{{ old('name', $team->name) }}"><br><br>

    <label>Descripción</label><br>
    <textarea name="description">{{ old('description', $team->description) }}</textarea><br><br>

    <button type="submit">Guardar cambios</button>
</form>

<br>
<a href="{{ route('torneos.show', $team->tournament_id) }}">Cancelar</a>
@endsection
