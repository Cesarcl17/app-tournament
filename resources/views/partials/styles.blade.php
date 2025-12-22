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
</style>
