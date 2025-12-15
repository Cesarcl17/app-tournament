@extends('layouts.app')

@section('content')
<h1>Editar torneo</h1>

<form method="POST" action="{{ route('torneos.update', $tournament) }}">
    @csrf
    @method('PUT')

    <label>
        Nombre<br>
        <input type="text" name="name" value="{{ old('name', $tournament->name) }}">
    </label>
    <br><br>

    <label>
        Descripción<br>
        <textarea name="description">{{ old('description', $tournament->description) }}</textarea>
    </label>
    <br><br>

    <label>
        Fecha inicio<br>
        <input type="date" name="start_date"
               value="{{ old('start_date', $tournament->start_date) }}">
    </label>
    <br><br>

    <label>
        Fecha fin<br>
        <input type="date" name="end_date"
               value="{{ old('end_date', $tournament->end_date) }}">
    </label>
    <br><br>

    <button type="submit">Guardar cambios</button>
</form>

<br>
<a href="{{ route('torneos.show', $tournament) }}">Cancelar</a>
@endsection
