@extends('layouts.app')

@section('title', 'Calendario de Partidas')

@section('content')
    <div class="page-header">
        <h1>📅 Calendario de Partidas</h1>
    </div>

    <div class="calendar-container">
        {{-- Filtros --}}
        <div class="calendar-filters card mb-2">
            <div class="card-header">Filtros</div>
            <div class="filters-grid">
                <div class="form-group">
                    <label for="filter-type">Ver partidas</label>
                    <select id="filter-type" class="form-control">
                        <option value="all">Todas las partidas</option>
                        @auth
                            <option value="my_matches">Mis partidas</option>
                            @if($userTeams->isNotEmpty())
                                <option value="my_team">Por equipo</option>
                            @endif
                        @endauth
                    </select>
                </div>

                @auth
                    <div class="form-group" id="team-filter-group" style="display: none;">
                        <label for="filter-team">Equipo</label>
                        <select id="filter-team" class="form-control">
                            @foreach($userTeams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->tournament->name }})</option>
                            @endforeach
                        </select>
                    </div>
                @endauth

                <div class="form-group">
                    <label for="filter-tournament">Torneo</label>
                    <select id="filter-tournament" class="form-control">
                        <option value="">Todos los torneos</option>
                        @foreach($tournaments as $tournament)
                            <option value="{{ $tournament->id }}">{{ $tournament->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="calendar-legend mb-2">
            <span class="legend-item">
                <span class="legend-color" style="background: #007bff;"></span> Pendiente
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: #ffc107;"></span> En progreso
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: #28a745;"></span> Completada
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: #dc3545;"></span> En disputa
            </span>
        </div>

        {{-- Calendario --}}
        <div class="card">
            <div id="calendar"></div>
        </div>
    </div>

    {{-- Modal de detalles del partido --}}
    <div id="match-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Detalles del Partido</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modal-game" class="modal-game"></div>
                <div class="modal-teams">
                    <span id="modal-team1" class="team-name"></span>
                    <span class="vs">VS</span>
                    <span id="modal-team2" class="team-name"></span>
                </div>
                <div id="modal-score" class="modal-score" style="display: none;"></div>
                <div class="modal-info">
                    <p><strong>Torneo:</strong> <span id="modal-tournament"></span></p>
                    <p><strong>Ronda:</strong> <span id="modal-round"></span></p>
                    <p><strong>Fecha:</strong> <span id="modal-date"></span></p>
                    <p><strong>Estado:</strong> <span id="modal-status"></span></p>
                    <p id="modal-winner-row" style="display: none;">
                        <strong>Ganador:</strong> <span id="modal-winner"></span>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <a id="modal-google-calendar" href="#" target="_blank" class="btn btn-primary">
                    📅 Añadir a Google Calendar
                </a>
                <a id="modal-view-tournament" href="#" class="btn btn-secondary">
                    Ver Torneo
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<style>
    .calendar-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .calendar-filters {
        margin-bottom: 1.5rem;
    }

    .filters-grid {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1rem;
    }

    .filters-grid .form-group {
        flex: 1;
        min-width: 200px;
        margin: 0;
    }

    .calendar-legend {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    #calendar {
        padding: 1rem;
    }

    .mb-2 {
        margin-bottom: 1rem;
    }

    /* Modal styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .modal-header h3 {
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }

    .modal-close:hover {
        color: #000;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-game {
        text-align: center;
        margin-bottom: 1rem;
    }

    .modal-game img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        border-radius: 8px;
    }

    .modal-teams {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .modal-teams .vs {
        color: #888;
        font-size: 0.9rem;
    }

    .modal-score {
        text-align: center;
        font-size: 2rem;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 1rem;
    }

    .modal-info p {
        margin: 0.5rem 0;
    }

    .modal-footer {
        display: flex;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
        justify-content: center;
    }

    .modal-footer .btn {
        flex: 1;
        text-align: center;
    }

    /* FullCalendar customization */
    .fc-event {
        cursor: pointer;
    }

    .fc-event:hover {
        opacity: 0.9;
    }

    .fc-toolbar-title {
        font-size: 1.25rem !important;
    }

    @media (max-width: 768px) {
        .filters-grid {
            flex-direction: column;
        }

        .calendar-legend {
            justify-content: center;
        }

        .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const filterType = document.getElementById('filter-type');
        const filterTeam = document.getElementById('filter-team');
        const filterTournament = document.getElementById('filter-tournament');
        const teamFilterGroup = document.getElementById('team-filter-group');

        let currentEventData = null;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                list: 'Lista'
            },
            events: function(info, successCallback, failureCallback) {
                fetchEvents(successCallback, failureCallback);
            },
            eventClick: function(info) {
                showMatchModal(info.event);
            },
            eventDidMount: function(info) {
                // Add tooltip
                info.el.title = info.event.extendedProps.tournament_name + ' - ' + info.event.extendedProps.round_name;
            }
        });

        calendar.render();

        function fetchEvents(successCallback, failureCallback) {
            const params = new URLSearchParams();
            params.append('filter', filterType.value);

            if (filterType.value === 'my_team' && filterTeam) {
                params.append('team_id', filterTeam.value);
            }

            if (filterTournament.value) {
                params.append('tournament_id', filterTournament.value);
            }

            fetch(`{{ route('calendario.matches') }}?${params.toString()}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        }

        function showMatchModal(event) {
            currentEventData = event;
            const props = event.extendedProps;

            document.getElementById('modal-title').textContent = event.title;
            document.getElementById('modal-team1').textContent = props.team1_name;
            document.getElementById('modal-team2').textContent = props.team2_name;
            document.getElementById('modal-tournament').textContent = props.tournament_name;
            document.getElementById('modal-round').textContent = props.round_name;
            document.getElementById('modal-date').textContent = new Date(event.start).toLocaleString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Status
            const statusLabels = {
                'pending': 'Pendiente',
                'in_progress': 'En progreso',
                'completed': 'Completada'
            };
            let statusText = statusLabels[props.status] || props.status;
            if (props.result_status === 'disputed') {
                statusText = '⚠️ En disputa';
            }
            document.getElementById('modal-status').textContent = statusText;

            // Game logo
            const gameDiv = document.getElementById('modal-game');
            if (props.game_logo) {
                gameDiv.innerHTML = `<img src="/images/games/${props.game_logo}" alt="${props.game_name}"><div>${props.game_name}</div>`;
            } else {
                gameDiv.innerHTML = `<div>🎮 ${props.game_name}</div>`;
            }

            // Score (if completed)
            const scoreDiv = document.getElementById('modal-score');
            if (props.score) {
                scoreDiv.textContent = props.score;
                scoreDiv.style.display = 'block';
            } else {
                scoreDiv.style.display = 'none';
            }

            // Winner (if completed)
            const winnerRow = document.getElementById('modal-winner-row');
            if (props.winner_name) {
                document.getElementById('modal-winner').textContent = props.winner_name;
                winnerRow.style.display = 'block';
            } else {
                winnerRow.style.display = 'none';
            }

            // Links
            document.getElementById('modal-google-calendar').href = props.google_calendar_url;
            document.getElementById('modal-view-tournament').href = `/torneos/${props.tournament_id}`;

            document.getElementById('match-modal').style.display = 'flex';
        }

        // Filter change handlers
        filterType.addEventListener('change', function() {
            if (teamFilterGroup) {
                teamFilterGroup.style.display = this.value === 'my_team' ? 'block' : 'none';
            }
            calendar.refetchEvents();
        });

        if (filterTeam) {
            filterTeam.addEventListener('change', function() {
                calendar.refetchEvents();
            });
        }

        filterTournament.addEventListener('change', function() {
            calendar.refetchEvents();
        });

        // Close modal when clicking outside
        document.getElementById('match-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    });

    function closeModal() {
        document.getElementById('match-modal').style.display = 'none';
    }
</script>
@endpush
