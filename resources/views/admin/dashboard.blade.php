@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="page-header">
        <h1>📊 Dashboard de Administración</h1>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon">👥</div>
            <div class="stat-card-value">{{ $stats['users'] }}</div>
            <div class="stat-card-label">Usuarios registrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🏆</div>
            <div class="stat-card-value">{{ $stats['tournaments'] }}</div>
            <div class="stat-card-label">Torneos totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">⚔️</div>
            <div class="stat-card-value">{{ $stats['teams'] }}</div>
            <div class="stat-card-label">Equipos creados</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🎮</div>
            <div class="stat-card-value">{{ $stats['completedMatches'] }}/{{ $stats['matches'] }}</div>
            <div class="stat-card-label">Partidas completadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🔥</div>
            <div class="stat-card-value">{{ $stats['activeTournaments'] }}</div>
            <div class="stat-card-label">Torneos activos</div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="dashboard-charts">
        <div class="chart-card">
            <h3>📈 Usuarios registrados por mes</h3>
            <div class="chart-container">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>🎯 Torneos por juego</h3>
            <div class="chart-container">
                <canvas id="tournamentsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="dashboard-charts mt-2">
        <div class="chart-card">
            <h3>📊 Estado de partidas</h3>
            <div class="chart-container">
                <canvas id="matchesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Secciones de información --}}
    <div class="mt-2">
        <div class="card">
            <div class="card-header">⚠️ Disputas activas</div>
            @if($activeDisputes->isEmpty())
                <p class="text-muted">No hay disputas pendientes de resolver.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Torneo</th>
                                <th>Equipos</th>
                                <th>Ronda</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeDisputes as $match)
                                <tr>
                                    <td>{{ $match->tournament->name }}</td>
                                    <td>{{ $match->team1?->name }} vs {{ $match->team2?->name }}</td>
                                    <td>{{ $match->getRoundName() }}</td>
                                    <td>
                                        <a href="{{ route('torneos.disputes', $match->tournament) }}" class="btn btn-sm btn-danger">
                                            Resolver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="dashboard-charts mt-2">
        <div class="card">
            <div class="card-header">🏆 Últimos torneos</div>
            @if($recentTournaments->isEmpty())
                <p class="text-muted">No hay torneos creados.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Juego</th>
                                <th>Inicio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTournaments as $tournament)
                                <tr>
                                    <td>
                                        <a href="{{ route('torneos.show', $tournament) }}">{{ $tournament->name }}</a>
                                    </td>
                                    <td>{{ $tournament->game?->name ?? '-' }}</td>
                                    <td>{{ $tournament->start_date }}</td>
                                    <td>
                                        @if($tournament->isFinished())
                                            <span class="badge badge-success">Finalizado</span>
                                        @elseif($tournament->hasBracket())
                                            <span class="badge badge-primary">En curso</span>
                                        @else
                                            <span class="badge badge-secondary">Preparación</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">👥 Últimos usuarios</div>
            @if($recentUsers->isEmpty())
                <p class="text-muted">No hay usuarios registrados.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                                <tr>
                                    <td>
                                        <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge badge-primary">{{ $user->role }}</span></td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para los gráficos
    const usersByMonth = @json($usersByMonth);
    const tournamentsByGame = @json($tournamentsByGame);
    const matchesByStatus = @json($matchesByStatus);

    // Detectar tema para colores
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#e2e8f0' : '#333';
    const gridColor = isDark ? '#2d3748' : '#e5e5e5';

    // Gráfico de usuarios por mes
    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: {
            labels: usersByMonth.map(item => item.label),
            datasets: [{
                label: 'Nuevos usuarios',
                data: usersByMonth.map(item => item.count),
                borderColor: '#4dabf7',
                backgroundColor: 'rgba(77, 171, 247, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: textColor }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                }
            }
        }
    });

    // Gráfico de torneos por juego
    new Chart(document.getElementById('tournamentsChart'), {
        type: 'bar',
        data: {
            labels: tournamentsByGame.map(item => item.label),
            datasets: [{
                label: 'Torneos',
                data: tournamentsByGame.map(item => item.count),
                backgroundColor: [
                    '#4dabf7',
                    '#51cf66',
                    '#fcc419',
                    '#ff6b6b',
                    '#cc5de8',
                    '#20c997'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                }
            }
        }
    });

    // Gráfico de partidas por estado
    new Chart(document.getElementById('matchesChart'), {
        type: 'doughnut',
        data: {
            labels: matchesByStatus.map(item => item.label),
            datasets: [{
                data: matchesByStatus.map(item => item.count),
                backgroundColor: [
                    '#fcc419',
                    '#4dabf7',
                    '#51cf66'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor }
                }
            }
        }
    });
</script>
@endpush
