@extends('layouts.app')

@section('title', 'Solicitudes - ' . $team->name)

@section('content')
    <div class="page-header">
        <h1>Solicitudes para {{ $team->name }}</h1>
        <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">Volver al equipo</a>
    </div>

    @if ($pendingRequests->isEmpty())
        <div class="card">
            <p class="text-muted">No hay solicitudes pendientes.</p>
        </div>
    @else
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Email</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingRequests as $request)
                    <tr>
                        <td><a href="{{ route('users.show', $request->user) }}">{{ $request->user->name }}</a></td>
                        <td>{{ $request->user->email }}</td>
                        <td>{{ $request->message ?? '-' }}</td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td class="actions-inline">
                            <form action="{{ route('teams.approveRequest', [$team, $request]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                            </form>
                            <form action="{{ route('teams.rejectRequest', [$team, $request]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Rechazar la solicitud de {{ $request->user->name }}?')">Rechazar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif

    <hr>

    <h2>Historial de solicitudes</h2>

    @php
        $processedRequests = $team->requests()->whereIn('status', ['approved', 'rejected'])->with('user')->latest()->get();
    @endphp

    @if ($processedRequests->isEmpty())
        <p class="text-muted">No hay solicitudes procesadas.</p>
    @else
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Estado</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($processedRequests as $request)
                    <tr>
                        <td><a href="{{ route('users.show', $request->user) }}">{{ $request->user->name }}</a></td>
                        <td>
                            @if ($request->status === 'approved')
                                <span class="badge badge-success">Aprobada</span>
                            @else
                                <span class="badge badge-danger">Rechazada</span>
                            @endif
                        </td>
                        <td>{{ $request->message ?? '-' }}</td>
                        <td>{{ $request->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
@endsection
