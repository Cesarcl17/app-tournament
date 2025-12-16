@extends('layouts.app')

@section('content')
    <h2>Torneos</h2>
    <a href="{{ route('torneos.create') }}">Crear torneo</a>
    <br><br>

    <ul>
        @foreach($tournaments as $tournament)
            <li>
                <a href="{{ route('torneos.show', $tournament) }}">
                    {{ $tournament->name }}
                </a>
                ({{ $tournament->start_date }} → {{ $tournament->end_date }})
            </li>

        @endforeach
    </ul>
@endsection
