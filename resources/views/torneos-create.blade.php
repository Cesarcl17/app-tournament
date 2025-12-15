@extends('layouts.app')

@section('content')
<h2>Crear torneo</h2>

<form method="POST" action="{{ route('torneos.store') }}">
    @csrf

    <div>
        <label>Nombre</label><br>
        <input type="text" name="name" required>
    </div>

    <div>
        <label>Descripción</label><br>
        <textarea name="description"></textarea>
    </div>

    <div>
        <label>Fecha inicio</label><br>
        <input type="date" name="start_date" required>
    </div>

    <div>
        <label>Fecha fin</label><br>
        <input type="date" name="end_date" required>
    </div>

    <br>
    <button type="submit">Crear torneo</button>
</form>
@endsection
