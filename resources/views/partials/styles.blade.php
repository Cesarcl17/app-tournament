<style>
    /* Reset básico */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: #333;
        width: 100%;
        min-height: 100vh;
        background: #f5f5f5;
    }

    /* Contenedor principal */
    .container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    main {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Navegación */
    .navbar {
        background: #fff;
        padding: 15px 20px;
        margin-bottom: 0;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .navbar-left {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .navbar-left a {
        text-decoration: none;
        color: #007bff;
        padding: 5px 0;
    }

    .navbar-left a:hover {
        text-decoration: underline;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .user-info {
        text-decoration: none;
        color: #333;
    }

    .user-info:hover {
        text-decoration: underline;
        color: #007bff;
    }

    /* Sistema de Notificaciones - Dropdown */
    .notifications-dropdown {
        position: relative;
    }

    .notifications-trigger {
        background: none;
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        padding: 5px 10px;
        position: relative;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .notifications-trigger:hover {
        background: #f0f0f0;
    }

    .notifications-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #dc3545;
        color: white;
        font-size: 0.65rem;
        font-weight: bold;
        padding: 2px 5px;
        border-radius: 10px;
        min-width: 16px;
        text-align: center;
    }

    .notifications-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        width: 320px;
        max-height: 400px;
        overflow: hidden;
        z-index: 1000;
    }

    .notifications-menu.show {
        display: block;
    }

    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
    }

    .notifications-header span {
        font-weight: bold;
        color: #333;
    }

    .btn-link {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        font-size: 0.85rem;
        padding: 0;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .notifications-list {
        max-height: 280px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        gap: 10px;
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        text-decoration: none;
        color: #333;
        transition: background 0.2s;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.unread {
        background: #e7f3ff;
        border-left: 3px solid #007bff;
    }

    .notification-item.read {
        opacity: 0.7;
    }

    .notification-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-message {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .notification-time {
        color: #666;
        font-size: 0.75rem;
    }

    .notification-empty {
        padding: 30px 15px;
        text-align: center;
        color: #666;
    }

    .notifications-footer {
        padding: 10px 15px;
        border-top: 1px solid #dee2e6;
        text-align: center;
        background: #f8f9fa;
    }

    .notifications-footer a {
        color: #007bff;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .notifications-footer a:hover {
        text-decoration: underline;
    }

    /* Página de Notificaciones */
    .notifications-filters {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .notifications-page-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .notification-card {
        display: flex;
        gap: 15px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #dee2e6;
    }

    .notification-card.unread {
        border-left-color: #007bff;
        background: #f8fbff;
    }

    .notification-card-icon {
        font-size: 2rem;
        flex-shrink: 0;
    }

    .notification-card-content {
        flex: 1;
    }

    .notification-card-message {
        margin: 0 0 5px 0;
        font-size: 1rem;
        color: #333;
    }

    .notification-card-details {
        margin: 0 0 8px 0;
        font-size: 0.9rem;
        color: #666;
    }

    .notification-card-time {
        color: #999;
        font-size: 0.8rem;
    }

    .notification-card-actions {
        display: flex;
        gap: 5px;
        align-items: flex-start;
        flex-shrink: 0;
    }

    .notification-detail-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .notification-detail-header {
        display: flex;
        gap: 15px;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .notification-detail-icon {
        font-size: 2.5rem;
    }

    .notification-detail-type {
        flex: 1;
        font-weight: bold;
        color: #333;
    }

    .notification-detail-time {
        color: #666;
        font-size: 0.9rem;
    }

    .notification-detail-body {
        padding: 25px;
    }

    .notification-detail-body h3 {
        margin: 0 0 15px 0;
        color: #333;
    }

    .notification-detail-body p {
        margin: 0 0 10px 0;
        color: #555;
        line-height: 1.6;
    }

    .notification-detail-footer {
        padding: 20px;
        border-top: 1px solid #dee2e6;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 576px) {
        .notifications-menu {
            width: 280px;
            right: -50px;
        }

        .notification-card {
            flex-direction: column;
        }

        .notification-card-actions {
            justify-content: flex-end;
        }
    }

    /* Navbar responsive */
    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: stretch;
            padding: 10px 15px;
        }

        .navbar-left {
            justify-content: center;
            padding-bottom: 10px;
        }

        .navbar-right {
            justify-content: center;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
    }

    /* Alertas */
    .alert {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border-color: #ffeeba;
    }

    .error-list {
        margin: 0;
        padding-left: 20px;
    }

    /* Tablas responsive */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 20px;
    }

    .table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
        margin-bottom: 0;
        background: #fff;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        white-space: nowrap;
    }

    .table th {
        background: #f8f9fa;
        font-weight: 600;
    }

    .table tr:hover {
        background: #f8f9fa;
    }

    @media (max-width: 768px) {
        .table th,
        .table td {
            padding: 8px 10px;
            font-size: 14px;
        }
    }

    /* Botones */
    .btn {
        display: inline-block;
        padding: 8px 16px;
        font-size: 14px;
        text-decoration: none;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 5px;
        color: white;
    }

    .btn:hover {
        color: white;
        text-decoration: none;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: 12px;
    }

    /* Formularios */
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .form-control:focus {
        outline: none;
        border-color: #80bdff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    select.form-control {
        cursor: pointer;
    }

    /* Acciones */
    .actions {
        margin: 20px 0;
    }

    .actions-inline {
        display: flex;
        gap: 5px;
        align-items: center;
        flex-wrap: wrap;
    }

    .actions-inline form {
        display: inline;
        margin: 0;
    }

    @media (max-width: 480px) {
        .actions-inline {
            flex-direction: column;
            width: 100%;
        }

        .actions-inline .btn,
        .actions-inline form {
            width: 100%;
        }

        .actions-inline form .btn {
            width: 100%;
        }
    }

    /* Contenido */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .page-header h1,
    .page-header h2 {
        margin: 0;
    }

    @media (max-width: 480px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header h1,
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header .btn {
            width: 100%;
        }
    }

    .card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .card-header {
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    @media (max-width: 480px) {
        .card {
            padding: 15px;
            border-radius: 0;
            margin-left: -20px;
            margin-right: -20px;
            border-left: none;
            border-right: none;
        }

        main {
            padding: 15px;
        }
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 3px 8px;
        font-size: 12px;
        border-radius: 3px;
    }

    .badge-primary {
        background: #007bff;
        color: white;
    }

    .badge-success {
        background: #28a745;
        color: white;
    }

    .badge-warning {
        background: #ffc107;
        color: #212529;
    }

    .badge-danger {
        background: #dc3545;
        color: white;
    }

    /* Links */
    a {
        color: #007bff;
    }

    a:hover {
        color: #0056b3;
    }

    /* Utilidades */
    .text-muted {
        color: #6c757d;
    }

    .text-center {
        text-align: center;
    }

    .mt-1 { margin-top: 10px; }
    .mt-2 { margin-top: 20px; }
    .mb-1 { margin-bottom: 10px; }
    .mb-2 { margin-bottom: 20px; }

    /* Utilidades responsive */
    .hide-mobile { display: block; }
    .show-mobile { display: none; }

    @media (max-width: 768px) {
        .hide-mobile { display: none !important; }
        .show-mobile { display: block !important; }
    }

    hr {
        border: none;
        border-top: 1px solid #dee2e6;
        margin: 20px 0;
    }

    /* ========== PÁGINA DE INICIO ========== */
    .hero-section {
        text-align: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .hero-section h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .hero-section p {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .section-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: #333;
    }

    /* Grid de juegos */
    .games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .game-card {
        display: block;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        color: inherit;
    }

    .game-card-logo {
        height: 160px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .game-card-logo img {
        max-width: 80%;
        max-height: 80%;
        object-fit: contain;
    }

    .game-card-placeholder {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .game-card-info {
        padding: 20px;
    }

    .game-card-info h3 {
        margin: 0 0 10px 0;
        font-size: 1.25rem;
        color: #333;
    }

    .game-card-description {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .game-card-stats {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Torneos preview */
    .tournaments-preview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    /* Filtros */
    .filters-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-chip {
        display: inline-block;
        padding: 8px 16px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: all 0.2s;
    }

    .filter-chip:hover {
        background: #e9ecef;
        text-decoration: none;
        color: #333;
    }

    .filter-chip.active {
        background: #007bff;
        border-color: #007bff;
        color: white;
    }

    .filter-chip.active:hover {
        background: #0056b3;
        color: white;
    }

    /* Responsive inicio */
    @media (max-width: 768px) {
        .hero-section {
            padding: 30px 15px;
        }

        .hero-section h1 {
            font-size: 1.8rem;
        }

        .hero-section p {
            font-size: 1rem;
        }

        .games-grid {
            grid-template-columns: 1fr;
        }

        .game-card-logo {
            height: 120px;
        }
    }

    /* ==========================================
       ESTILOS DEL BRACKET
       ========================================== */

    /* Banner del Campeón */
    .champion-banner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%);
        border-radius: 12px;
        padding: 20px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(255, 183, 0, 0.4);
    }

    .champion-icon {
        font-size: 3rem;
    }

    .champion-info {
        display: flex;
        flex-direction: column;
    }

    .champion-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #7a5800;
    }

    .champion-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }

    /* Contenedor principal del bracket */
    .bracket-container {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding: 20px 10px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    /* Cada ronda del bracket */
    .bracket-round {
        display: flex;
        flex-direction: column;
        min-width: 280px;
    }

    .bracket-round-title {
        text-align: center;
        font-weight: bold;
        font-size: 1rem;
        color: #555;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .bracket-matches {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        flex: 1;
        gap: 15px;
    }

    /* Cada partida */
    .bracket-match {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.2s ease;
    }

    .bracket-match:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .bracket-match.match-completed {
        border-color: #28a745;
    }

    .bracket-match.match-pending {
        border-color: #ffc107;
    }

    .bracket-match.match-bye {
        border-color: #6c757d;
        background: #e9ecef;
    }

    /* Equipos dentro de la partida */
    .bracket-team {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #fff;
        border-radius: 4px;
        margin: 4px 0;
        border-left: 4px solid transparent;
    }

    .bracket-team.team-winner {
        background: #d4edda;
        border-left-color: #28a745;
        font-weight: bold;
    }

    .bracket-team.team-loser {
        opacity: 0.6;
        border-left-color: #dc3545;
    }

    .team-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .team-score {
        background: #007bff;
        color: white;
        font-weight: bold;
        padding: 2px 10px;
        border-radius: 4px;
        margin-left: 10px;
        min-width: 30px;
        text-align: center;
    }

    .bracket-team.team-winner .team-score {
        background: #28a745;
    }

    .bracket-team.team-loser .team-score {
        background: #dc3545;
    }

    /* VS separator */
    .bracket-vs {
        text-align: center;
        font-size: 0.75rem;
        color: #999;
        font-weight: bold;
        padding: 2px 0;
    }

    /* Fecha programada */
    .bracket-schedule {
        text-align: center;
        font-size: 0.8rem;
        color: #666;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px dashed #dee2e6;
    }

    /* Acciones de la partida (admin/organizador) */
    .bracket-actions-match {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #dee2e6;
    }

    .match-result-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .result-inputs {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .score-input {
        width: 50px;
        padding: 5px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1rem;
    }

    .winner-select {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .winner-select label {
        font-size: 0.85rem;
        color: #666;
    }

    .winner-select select {
        flex: 1;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .schedule-form {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    .schedule-form input[type="datetime-local"] {
        flex: 1;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 0.85rem;
    }

    /* Conector visual entre partidas */
    .bracket-connector {
        height: 20px;
    }

    /* Acciones del bracket (botón generar, etc) */
    .bracket-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
    }

    /* Leyenda del bracket */
    .bracket-legend {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .bracket-legend h4 {
        margin-bottom: 15px;
        color: #333;
    }

    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .legend-winner {
        background: #d4edda;
        border-color: #28a745;
    }

    .legend-loser {
        background: #f8d7da;
        border-color: #dc3545;
    }

    .legend-pending {
        background: #fff3cd;
        border-color: #ffc107;
    }

    .legend-bye {
        background: #e9ecef;
        border-color: #6c757d;
    }

    /* Disputas y estados del match */
    .bracket-match.match-disputed {
        border-color: #dc3545;
        background: linear-gradient(135deg, #fff 0%, #ffe6e6 100%);
        box-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
    }

    .match-status-bar {
        text-align: center;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #dee2e6;
    }

    .captain-report {
        background: #f0f9ff;
        border-radius: 6px;
        padding: 10px;
        margin-top: 10px;
    }

    .admin-actions {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 10px;
    }

    .admin-actions details {
        margin-bottom: 10px;
    }

    .admin-actions details summary {
        cursor: pointer;
    }

    .dispute-info {
        background: #ffe6e6;
        border: 1px solid #dc3545;
        border-radius: 4px;
        padding: 8px;
        margin-bottom: 10px;
        font-size: 0.85rem;
    }

    .dispute-info p {
        margin: 4px 0;
    }

    .result-team {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .result-team small {
        font-size: 0.7rem;
        color: #666;
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .legend-disputed {
        background: linear-gradient(135deg, #fff 0%, #ffe6e6 100%);
        border-color: #dc3545;
    }

    /* Página de disputas */
    .disputes-container {
        margin-top: 20px;
    }

    .dispute-card {
        background: #fff;
        border: 2px solid #dc3545;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
    }

    .dispute-card h4 {
        color: #dc3545;
        margin-bottom: 15px;
    }

    .dispute-reports {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }

    .dispute-report {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 12px;
    }

    .dispute-report h5 {
        font-size: 0.9rem;
        color: #333;
        margin-bottom: 8px;
    }

    .dispute-score {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: center;
        color: #495057;
    }

    .dispute-actions {
        border-top: 1px solid #dee2e6;
        padding-top: 15px;
    }

    @media (max-width: 576px) {
        .dispute-reports {
            grid-template-columns: 1fr;
        }
    }

    /* Responsive bracket */
    @media (max-width: 768px) {
        .bracket-container {
            padding: 10px;
        }

        .bracket-round {
            min-width: 240px;
        }

        .bracket-round-title {
            font-size: 0.85rem;
        }

        .champion-banner {
            flex-direction: column;
            text-align: center;
            padding: 15px;
        }

        .champion-icon {
            font-size: 2.5rem;
        }

        .champion-name {
            font-size: 1.2rem;
        }

        .legend-items {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
