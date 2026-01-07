<style>
    /* ========================================
       CSS VARIABLES - TEMA CLARO
       Estilo inspirado en Challonge con turquesa
       ======================================== */
    :root {
        /* Colores principales - Turquesa como acento */
        --color-primary: #00b8a9;
        --color-primary-hover: #00a396;
        --color-primary-light: #00d4c8;
        --color-secondary: #6c757d;
        --color-secondary-hover: #545b62;
        --color-success: #00b894;
        --color-success-hover: #00a382;
        --color-danger: #e74c3c;
        --color-danger-hover: #c0392b;
        --color-warning: #f39c12;
        --color-warning-text: #1a1a2e;

        /* Fondos - Grises claros */
        --bg-body: #f0f2f5;
        --bg-main: #e8eaed;
        --bg-card: #ffffff;
        --bg-card-secondary: #f8f9fa;
        --bg-input: #ffffff;
        --bg-navbar: #ffffff;
        --bg-hover: #e9ecef;
        --bg-footer: #2d3436;

        /* Textos */
        --text-primary: #2d3436;
        --text-secondary: #636e72;
        --text-muted: #b2bec3;
        --text-light: #dfe6e9;
        --text-on-primary: #ffffff;

        /* Bordes */
        --border-color: #dfe6e9;
        --border-light: #e9ecef;
        --border-dark: #b2bec3;

        /* Sombras */
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
        --shadow-lg: 0 8px 25px rgba(0,0,0,0.12);
        --shadow-xl: 0 20px 40px rgba(0,0,0,0.15);

        /* Gradientes */
        --gradient-hero: linear-gradient(135deg, #00b8a9 0%, #0984e3 100%);
        --gradient-game-card: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
        --gradient-champion: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --gradient-primary: linear-gradient(135deg, #00b8a9 0%, #00d4c8 100%);

        /* Notificaciones */
        --notification-unread-bg: #e0f7f6;

        /* Alertas */
        --alert-success-bg: #d5f4e6;
        --alert-success-text: #00875a;
        --alert-success-border: #00b894;
        --alert-error-bg: #fde8e8;
        --alert-error-text: #c0392b;
        --alert-error-border: #e74c3c;
        --alert-warning-bg: #fef3e2;
        --alert-warning-text: #b7791f;
        --alert-warning-border: #f39c12;

        /* Bracket */
        --bracket-winner-bg: #d5f4e6;
        --bracket-loser-bg: #fde8e8;
        --bracket-match-bg: #ffffff;
        --bracket-connector: #b2bec3;
    }

    /* ========================================
       CSS VARIABLES - TEMA OSCURO
       Estilo Challonge con variantes de negro
       ======================================== */
    [data-theme="dark"] {
        /* Colores principales - Turquesa brillante */
        --color-primary: #00e5cc;
        --color-primary-hover: #00ffdd;
        --color-primary-light: #33ffe0;
        --color-secondary: #a0aec0;
        --color-secondary-hover: #cbd5e0;
        --color-success: #00e676;
        --color-success-hover: #00ff88;
        --color-danger: #ff5252;
        --color-danger-hover: #ff7070;
        --color-warning: #ffb300;
        --color-warning-text: #0d0d0d;

        /* Fondos - Variantes de negro profundo */
        --bg-body: #0d0d0d;
        --bg-main: #121212;
        --bg-card: #1a1a1a;
        --bg-card-secondary: #242424;
        --bg-input: #1e1e1e;
        --bg-navbar: #0d0d0d;
        --bg-hover: #2a2a2a;
        --bg-footer: #0d0d0d;

        /* Textos */
        --text-primary: #f5f5f5;
        --text-secondary: #b3b3b3;
        --text-muted: #666666;
        --text-light: #404040;
        --text-on-primary: #0d0d0d;

        /* Bordes */
        --border-color: #333333;
        --border-light: #2a2a2a;
        --border-dark: #404040;

        /* Sombras */
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.4);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.5);
        --shadow-lg: 0 8px 25px rgba(0,0,0,0.6);
        --shadow-xl: 0 20px 40px rgba(0,0,0,0.7);

        /* Gradientes */
        --gradient-hero: linear-gradient(135deg, #00b8a9 0%, #006b6b 100%);
        --gradient-game-card: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        --gradient-champion: linear-gradient(135deg, #f1c40f 0%, #c9a00c 100%);
        --gradient-primary: linear-gradient(135deg, #00e5cc 0%, #00b8a9 100%);

        /* Notificaciones */
        --notification-unread-bg: #0a2f2f;

        /* Alertas */
        --alert-success-bg: #0a3622;
        --alert-success-text: #6ee7b7;
        --alert-success-border: #00e676;
        --alert-error-bg: #3d1515;
        --alert-error-text: #fca5a5;
        --alert-error-border: #ff5252;
        --alert-warning-bg: #422006;
        --alert-warning-text: #fcd34d;
        --alert-warning-border: #ffb300;

        /* Bracket */
        --bracket-winner-bg: #0a3622;
        --bracket-loser-bg: #3d1515;
        --bracket-match-bg: #1a1a1a;
        --bracket-connector: #404040;
    }

    /* Reset básico */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: var(--text-primary);
        width: 100%;
        min-height: 100vh;
        background: var(--bg-body);
        transition: background-color 0.3s ease, color 0.3s ease;
        font-size: 14px;
    }

    /* Contenedor principal */
    .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    main {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    /* Navegación estilo Challonge */
    .navbar {
        background: var(--bg-navbar);
        padding: 0 24px;
        height: 64px;
        margin-bottom: 0;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
        gap: 16px;
        position: sticky;
        top: 0;
        z-index: 100;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    [data-theme="light"] .navbar {
        background: rgba(255, 255, 255, 0.95);
    }

    [data-theme="dark"] .navbar {
        background: rgba(18, 18, 18, 0.95);
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: nowrap;
    }

    .navbar-left a {
        text-decoration: none;
        color: var(--text-secondary);
        padding: 8px 0;
        font-weight: 500;
        font-size: 14px;
        transition: color 0.2s;
        white-space: nowrap;
    }

    .navbar-left a:hover {
        color: var(--color-primary);
        text-decoration: none;
    }

    /* Logo / Brand con corona de laurel */
    .navbar-brand {
        display: flex;
        align-items: center;
        text-decoration: none !important;
        margin-right: 20px;
        padding: 4px 0;
    }

    .navbar-brand:hover {
        text-decoration: none !important;
    }

    .navbar-brand:hover .laurel-left,
    .navbar-brand:hover .laurel-right {
        transform: scaleY(1.05);
    }

    .navbar-brand:hover .brand-text-bottom {
        text-shadow: 0 0 20px rgba(0, 229, 204, 0.5);
    }

    .brand-laurel-wrapper {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .brand-laurel-wrapper-mobile {
        display: none;
        align-items: center;
        gap: 2px;
    }

    .laurel-left,
    .laurel-right {
        width: 16px;
        height: 40px;
        transition: transform 0.3s ease;
    }

    .laurel-left-sm,
    .laurel-right-sm {
        width: 14px;
        height: 32px;
    }

    .brand-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        line-height: 1;
        padding: 0 4px;
    }

    .brand-text-top {
        font-size: 8px;
        font-weight: 700;
        color: var(--text-secondary);
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .brand-text-bottom {
        font-size: 18px;
        font-weight: 900;
        background: linear-gradient(135deg, #00e5cc 0%, #00b8a9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 3px;
        transition: text-shadow 0.3s ease;
    }

    .brand-text-short {
        font-size: 18px;
        font-weight: 900;
        background: linear-gradient(135deg, #00e5cc 0%, #00b8a9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 2px;
    }

    @media (max-width: 768px) {
        .brand-laurel-wrapper {
            display: none;
        }

        .brand-laurel-wrapper-mobile {
            display: flex;
        }
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .user-info {
        text-decoration: none;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .user-info:hover {
        background: var(--bg-hover);
        text-decoration: none;
        color: var(--text-primary);
    }

    /* Toggle de Tema */
    .theme-toggle {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 8px;
        border-radius: 6px;
        transition: background 0.2s;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .theme-toggle:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    /* ==========================================
       NUEVO NAVBAR MODERNO
       ========================================== */
    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .nav-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none !important;
        color: var(--text-primary) !important;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .nav-brand:hover {
        text-decoration: none !important;
    }

    .nav-logo {
        font-size: 1.5rem;
    }

    .nav-brand-text {
        background: linear-gradient(135deg, var(--color-primary) 0%, #00d4aa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-links a {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .nav-links a:hover {
        color: var(--color-primary);
        background: var(--bg-hover);
        text-decoration: none;
    }

    .nav-links a.active {
        color: var(--color-primary);
        background: rgba(0, 184, 169, 0.1);
    }

    .nav-icon {
        font-style: normal;
        font-size: 1rem;
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-user-name {
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.9rem;
    }

    /* Hamburguesa */
    .nav-toggle {
        display: none;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        padding: 8px;
        background: none;
        border: none;
        cursor: pointer;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .nav-toggle:hover {
        background: var(--bg-hover);
    }

    .hamburger-line {
        width: 22px;
        height: 2px;
        background: var(--text-primary);
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    .nav-toggle.active .hamburger-line:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .nav-toggle.active .hamburger-line:nth-child(2) {
        opacity: 0;
    }

    .nav-toggle.active .hamburger-line:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    /* Botón icono en navbar (Admin, etc.) */
    .navbar-icon-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 8px;
        border-radius: 6px;
        transition: background 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .navbar-icon-btn:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    /* Notificaciones trigger */
    .notifications-trigger {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 8px;
        position: relative;
        border-radius: 6px;
        transition: background 0.2s;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .notifications-trigger:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    .notifications-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: var(--color-danger);
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
        top: calc(100% + 8px);
        right: 0;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        width: 360px;
        max-height: 450px;
        overflow: hidden;
        z-index: 1000;
    }

    [data-theme="dark"] .notifications-menu {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    }

    .notifications-menu.show {
        display: block;
        animation: dropdownSlide 0.2s ease;
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-card-secondary);
    }

    .notifications-header span {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 15px;
    }

    .btn-link {
        background: none;
        border: none;
        color: var(--color-primary);
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
        border-bottom: 1px solid var(--border-light);
        text-decoration: none;
        color: var(--text-primary);
        transition: background 0.2s;
    }

    .notification-item:hover {
        background: var(--bg-card-secondary);
    }

    .notification-item.unread {
        background: var(--notification-unread-bg);
        border-left: 3px solid var(--color-primary);
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
        color: var(--text-secondary);
        font-size: 0.75rem;
    }

    .notification-empty {
        padding: 30px 15px;
        text-align: center;
        color: var(--text-secondary);
    }

    .notifications-footer {
        padding: 10px 15px;
        border-top: 1px solid var(--border-color);
        text-align: center;
        background: var(--bg-card-secondary);
    }

    .notifications-footer a {
        color: var(--color-primary);
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
        padding: 20px;
        background: var(--bg-card);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border-left: 4px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .notification-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }

    .notification-card.unread {
        border-left-color: var(--color-primary);
        background: var(--notification-unread-bg);
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
        color: var(--text-primary);
    }

    .notification-card-details {
        margin: 0 0 8px 0;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .notification-card-time {
        color: var(--text-light);
        font-size: 0.8rem;
    }

    .notification-card-actions {
        display: flex;
        gap: 5px;
        align-items: flex-start;
        flex-shrink: 0;
    }

    .notification-detail-card {
        background: var(--bg-card);
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .notification-detail-header {
        display: flex;
        gap: 15px;
        align-items: center;
        padding: 20px;
        background: var(--bg-card-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .notification-detail-icon {
        font-size: 2.5rem;
    }

    .notification-detail-type {
        flex: 1;
        font-weight: bold;
        color: var(--text-primary);
    }

    .notification-detail-time {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .notification-detail-body {
        padding: 25px;
    }

    .notification-detail-body h3 {
        margin: 0 0 15px 0;
        color: var(--text-primary);
    }

    .notification-detail-body p {
        margin: 0 0 10px 0;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .notification-detail-footer {
        padding: 20px;
        border-top: 1px solid var(--border-color);
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
            border-top: 1px solid var(--border-color);
        }
    }

    /* Alertas estilo Challonge */
    .alert {
        padding: 16px 20px;
        margin-bottom: 20px;
        border-radius: 16px;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .alert-success {
        background: var(--alert-success-bg);
        color: var(--alert-success-text);
        border-color: var(--alert-success-border);
    }

    .alert-error {
        background: var(--alert-error-bg);
        color: var(--alert-error-text);
        border-color: var(--alert-error-border);
    }

    .alert-warning {
        background: var(--alert-warning-bg);
        color: var(--alert-warning-text);
        border-color: var(--alert-warning-border);
    }

    .alert-info {
        background: rgba(52, 152, 219, 0.15);
        color: #3498db;
        border-color: rgba(52, 152, 219, 0.3);
    }

    [data-theme="dark"] .alert {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
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
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        background: var(--bg-card);
        border-radius: 12px;
        overflow: hidden;
    }

    .table th,
    .table td {
        padding: 16px 20px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .table th {
        background: var(--bg-card-secondary);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
    }

    .table tr {
        transition: background 0.2s ease;
    }

    .table tbody tr:hover {
        background: var(--bg-hover);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .table th,
        .table td {
            padding: 8px 10px;
            font-size: 14px;
        }
    }

    /* Botones estilo Challonge - Pill shape */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        margin-right: 8px;
        color: white;
        transition: all 0.25s ease;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    }

    .btn:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background: var(--gradient-primary);
        color: var(--text-on-primary);
        box-shadow: 0 4px 15px rgba(0, 229, 204, 0.3);
    }

    .btn-primary:hover {
        background: var(--color-primary-light);
        color: var(--text-on-primary);
        box-shadow: 0 8px 25px rgba(0, 229, 204, 0.4);
    }

    .btn-secondary {
        background: var(--bg-card-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    .btn-danger {
        background: var(--color-danger);
        color: white;
    }

    .btn-danger:hover {
        background: var(--color-danger-hover);
    }

    .btn-success {
        background: var(--color-success);
        color: white;
    }

    .btn-success:hover {
        background: var(--color-success-hover);
    }

    .btn-warning {
        background: var(--color-warning);
        color: var(--color-warning-text);
    }

    .btn-warning:hover {
        background: #e8a00c;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid var(--color-primary);
        color: var(--color-primary);
    }

    .btn-outline:hover {
        background: var(--color-primary);
        color: white;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 12px;
        border-radius: 50px;
    }

    .btn-lg {
        padding: 14px 28px;
        font-size: 16px;
        border-radius: 10px;
    }

    /* Formularios estilo Challonge */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        font-size: 14px;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-input);
        color: var(--text-primary);
        transition: all 0.25s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px rgba(0, 229, 204, 0.15);
    }

    .form-control:hover:not(:focus) {
        border-color: var(--border-dark);
    }

    .form-control.is-invalid {
        border-color: var(--color-danger);
    }

    .invalid-feedback {
        color: var(--color-danger);
        font-size: 13px;
        margin-top: 6px;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23999' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-color: var(--bg-input);
        padding-right: 40px;
    }

    [data-theme="dark"] select.form-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23b3b3b3' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    }

    select.form-control option {
        background: var(--bg-card);
        color: var(--text-primary);
    }

    .form-text {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-hint {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-hint .hint-icon {
        cursor: help;
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

    /* Page Header estilo Challonge */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .page-header h1,
    .page-header h2 {
        margin: 0;
        color: var(--text-primary);
        font-weight: 700;
        font-size: 1.75rem;
        letter-spacing: -0.5px;
    }

    .page-header .actions-inline {
        display: flex;
        gap: 8px;
        align-items: center;
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

    /* Cards estilo Challonge con glow */
    .card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .card:hover {
        border-color: var(--color-primary);
        box-shadow: 0 8px 30px rgba(0, 229, 204, 0.1);
    }

    [data-theme="dark"] .card:hover {
        box-shadow: 0 8px 30px rgba(0, 229, 204, 0.15);
    }

    .card-header {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 480px) {
        .card {
            padding: 16px;
            border-radius: 0;
            margin-left: -24px;
            margin-right: -24px;
            border-left: none;
            border-right: none;
        }

        main {
            padding: 16px;
        }
    }

    /* Badges estilo Challonge - Pill shape */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        gap: 4px;
    }

    .badge-primary {
        background: rgba(0, 184, 169, 0.2);
        color: var(--color-primary);
        border: 1px solid rgba(0, 184, 169, 0.3);
    }

    .badge-success {
        background: rgba(0, 184, 148, 0.2);
        color: var(--color-success);
        border: 1px solid rgba(0, 184, 148, 0.3);
    }

    .badge-warning {
        background: rgba(243, 156, 18, 0.2);
        color: var(--color-warning);
        border: 1px solid rgba(243, 156, 18, 0.3);
    }

    .badge-danger {
        background: rgba(231, 76, 60, 0.2);
        color: var(--color-danger);
        border: 1px solid rgba(231, 76, 60, 0.3);
    }

    .badge-secondary {
        background: var(--bg-card-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .badge-info {
        background: rgba(52, 152, 219, 0.2);
        color: #3498db;
        border: 1px solid rgba(52, 152, 219, 0.3);
    }

    [data-theme="dark"] .badge-primary {
        background: rgba(0, 229, 204, 0.15);
        border-color: rgba(0, 229, 204, 0.3);
    }

    [data-theme="dark"] .badge-success {
        background: rgba(0, 230, 118, 0.15);
        border-color: rgba(0, 230, 118, 0.3);
    }

    [data-theme="dark"] .badge-warning {
        background: rgba(255, 179, 0, 0.15);
        border-color: rgba(255, 179, 0, 0.3);
    }

    [data-theme="dark"] .badge-danger {
        background: rgba(255, 82, 82, 0.15);
        border-color: rgba(255, 82, 82, 0.3);
    }

    [data-theme="dark"] .badge-info {
        background: rgba(52, 152, 219, 0.15);
        border-color: rgba(52, 152, 219, 0.3);
    }

    /* Links */
    a {
        color: var(--color-primary);
        text-decoration: none;
        transition: color 0.2s;
    }

    a:hover {
        color: var(--color-primary-hover);
    }

    /* Utilidades */
    .text-muted {
        color: var(--text-muted);
    }

    .text-center {
        text-align: center;
    }

    .mt-1 { margin-top: 10px; }
    .mt-2 { margin-top: 20px; }
    .mt-3 { margin-top: 30px; }
    .mb-1 { margin-bottom: 10px; }
    .mb-2 { margin-bottom: 20px; }
    .mb-3 { margin-bottom: 30px; }

    /* Utilidades responsive */
    .hide-mobile { display: block; }
    .show-mobile { display: none; }

    @media (max-width: 768px) {
        .hide-mobile { display: none !important; }
        .show-mobile { display: block !important; }
    }

    hr {
        border: none;
        border-top: 1px solid var(--border-color);
        margin: 20px 0;
    }

    /* ========== PÁGINA DE INICIO ESTILO CHALLONGE ========== */
    .hero-section {
        text-align: center;
        padding: 100px 24px;
        background: linear-gradient(135deg, #00b8a9 0%, #006b6b 50%, #004d4d 100%);
        color: white;
        border-radius: 20px;
        margin-bottom: 48px;
        position: relative;
        overflow: hidden;
        background-image: url('/images/hero-bg.svg');
        background-size: cover;
        background-position: center;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: heroFloat 25s linear infinite;
        pointer-events: none;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(ellipse at center, transparent 0%, rgba(0,0,0,0.3) 100%);
        pointer-events: none;
    }

    @keyframes heroFloat {
        0% { transform: translate(0, 0); }
        100% { transform: translate(40px, 40px); }
    }

    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        letter-spacing: -2px;
        line-height: 1.1;
    }

    .hero-section p {
        font-size: 1.3rem;
        opacity: 0.9;
        max-width: 650px;
        margin: 0 auto 32px;
        position: relative;
        z-index: 1;
        font-weight: 400;
        line-height: 1.6;
    }

    .hero-section .btn {
        position: relative;
        z-index: 1;
        font-size: 1rem;
        padding: 16px 40px;
        background: white;
        color: #00b8a9;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    }

    .hero-section .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 60px 20px;
            border-radius: 0;
            margin-left: -24px;
            margin-right: -24px;
            margin-top: -24px;
        }

        .hero-section h1 {
            font-size: 2rem;
        }

        .hero-section p {
            font-size: 1rem;
        }
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 24px;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-color);
    }

    /* Grid de juegos estilo Challonge */
    .games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 48px;
    }

    .game-card {
        display: block;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .game-card:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: var(--color-primary);
        text-decoration: none;
        color: inherit;
        box-shadow: 0 20px 40px rgba(0, 229, 204, 0.15),
                    0 0 60px rgba(0, 229, 204, 0.1);
    }

    [data-theme="dark"] .game-card:hover {
        box-shadow: 0 20px 40px rgba(0, 229, 204, 0.2),
                    0 0 80px rgba(0, 229, 204, 0.15);
    }

    .game-card-logo {
        height: 180px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .game-card-logo::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: linear-gradient(to top, var(--bg-card), transparent);
    }

    .game-card-logo img {
        max-width: 85%;
        max-height: 85%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .game-card:hover .game-card-logo img {
        transform: scale(1.05);
    }

    .game-card-placeholder {
        width: 100px;
        height: 100px;
        background: rgba(0, 184, 169, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-primary);
        font-size: 2rem;
        font-weight: bold;
    }

    .game-card-info {
        padding: 24px;
    }

    .game-card-info h3 {
        margin: 0 0 12px 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .game-card-description {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 16px;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .game-card-stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Torneos preview estilo Challonge */
    .tournaments-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
    }

    /* Filtros estilo Challonge */
    .filters-bar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 24px;
        padding: 16px 0;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        background: var(--bg-card);
        border: 2px solid var(--border-color);
        border-radius: 25px;
        text-decoration: none;
        color: var(--text-primary);
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .filter-chip:hover {
        background: var(--bg-hover);
        border-color: var(--color-primary);
        text-decoration: none;
        color: var(--text-primary);
    }

    .filter-chip.active {
        background: var(--color-primary);
        border-color: var(--color-primary);
        color: white;
        font-weight: 600;
    }

    .filter-chip.active:hover {
        background: var(--color-primary-hover);
        color: white;
    }

    /* Responsive inicio */
    @media (max-width: 768px) {
        .games-grid {
            grid-template-columns: 1fr;
        }

        .game-card-logo {
            height: 140px;
        }

        .filters-bar {
            padding: 12px 0;
        }

        .filter-chip {
            padding: 8px 16px;
            font-size: 13px;
        }
    }

    /* ==========================================
       ESTILOS DEL BRACKET - ESTILO CHALLONGE
       ========================================== */

    /* Banner del Campeón */
    .champion-banner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        background: linear-gradient(135deg, #ffd700 0%, #ffb700 50%, #ff9500 100%);
        border-radius: 16px;
        padding: 24px 40px;
        margin-bottom: 32px;
        box-shadow: 0 8px 24px rgba(255, 183, 0, 0.35);
    }

    .champion-icon {
        font-size: 3.5rem;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .champion-info {
        display: flex;
        flex-direction: column;
    }

    .champion-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #7a5800;
        font-weight: 600;
    }

    .champion-name {
        font-size: 1.75rem;
        font-weight: 800;
        color: #333;
    }

    /* Contenedor principal del bracket */
    .bracket-container {
        display: flex;
        gap: 24px;
        overflow-x: auto;
        padding: 24px;
        background: var(--bg-card);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 32px;
        border: 1px solid var(--border-color);
    }

    /* Cada ronda del bracket */
    .bracket-round {
        display: flex;
        flex-direction: column;
        min-width: 300px;
    }

    .bracket-round-title {
        text-align: center;
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--text-secondary);
        padding: 12px 16px;
        background: var(--bg-card-secondary);
        border-radius: 8px;
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .bracket-matches {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        flex: 1;
        gap: 16px;
    }

    /* Cada partida */
    .bracket-match {
        background: var(--bg-card-secondary);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bracket-match:hover {
        box-shadow: 0 12px 30px rgba(0, 229, 204, 0.12);
        transform: translateY(-4px);
        border-color: var(--color-primary);
    }

    [data-theme="dark"] .bracket-match:hover {
        box-shadow: 0 12px 30px rgba(0, 229, 204, 0.18);
    }

    .bracket-match.match-completed {
        border-color: var(--color-success);
    }

    .bracket-match.match-pending {
        border-color: var(--color-warning);
    }

    .bracket-match.match-bye {
        border-color: var(--color-secondary);
        background: var(--bg-hover);
        opacity: 0.7;
    }

    /* Equipos dentro de la partida */
    .bracket-team {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        background: var(--bg-card);
        border-radius: 8px;
        margin: 6px 0;
        border-left: 4px solid transparent;
        transition: all 0.2s ease;
    }

    .bracket-team:hover {
        background: var(--bg-hover);
    }

    .bracket-team.team-winner {
        background: var(--bracket-winner-bg);
        border-left-color: var(--color-success);
        font-weight: 700;
    }

    .bracket-team.team-loser {
        opacity: 0.5;
        border-left-color: var(--color-danger);
    }

    .team-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--text-primary);
        font-weight: 500;
    }

    .team-score {
        background: var(--color-primary);
        color: white;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 6px;
        margin-left: 12px;
        min-width: 32px;
        text-align: center;
        font-size: 0.9rem;
    }

    .bracket-team.team-winner .team-score {
        background: var(--color-success);
    }

    .bracket-team.team-loser .team-score {
        background: var(--color-danger);
    }

    /* VS separator */
    .bracket-vs {
        text-align: center;
        font-size: 0.7rem;
        color: var(--text-muted);
        font-weight: 700;
        padding: 4px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Fecha programada */
    .bracket-schedule {
        text-align: center;
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed var(--border-color);
    }

    /* Link a comentarios en bracket */
    .bracket-comments-link {
        text-align: center;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--border-color);
    }

    .comments-link {
        font-size: 0.85rem;
        color: var(--color-primary);
        text-decoration: none;
    }

    .comments-link:hover {
        text-decoration: underline;
    }

    /* Check-in section en bracket */
    .bracket-checkin {
        margin-top: 8px;
        padding: 8px;
        background: var(--bg-card);
        border-radius: 4px;
        border: 1px dashed var(--border-color);
    }

    .checkin-header {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        cursor: help;
    }

    .checkin-teams {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 8px;
    }

    .checkin-team {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        padding: 4px 6px;
        background: var(--bg-main);
        border-radius: 4px;
        color: var(--text-secondary);
    }

    .checkin-team.checked-in {
        background: rgba(81, 207, 102, 0.15);
        color: var(--color-success);
    }

    .checkin-icon {
        font-size: 0.9rem;
    }

    .checkin-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .checkin-form {
        text-align: center;
    }

    .checkin-ready {
        text-align: center;
        font-size: 0.85rem;
        color: var(--color-success);
        font-weight: 600;
        margin-top: 4px;
    }

    .checkin-done {
        color: var(--color-success);
    }

    .checkin-pending {
        color: var(--text-muted);
    }

    .checkin-expired {
        color: var(--color-danger);
    }

    .btn-checkin {
        width: 100%;
        margin-top: 4px;
    }

    /* Acciones de la partida (admin/organizador) */
    .bracket-actions-match {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border-color);
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
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 1rem;
        background: var(--bg-input);
        color: var(--text-primary);
    }

    .winner-select {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .winner-select label {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .winner-select select {
        flex: 1;
        padding: 5px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: var(--bg-input);
        color: var(--text-primary);
    }

    .schedule-form {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    .schedule-form input[type="datetime-local"] {
        flex: 1;
        padding: 5px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 0.85rem;
        background: var(--bg-input);
        color: var(--text-primary);
    }

    /* Conector visual entre partidas */
    .bracket-connector {
        height: 20px;
    }

    /* Acciones del bracket (botón generar, etc) */
    .bracket-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
    }

    /* Leyenda del bracket */
    .bracket-legend {
        background: var(--bg-card);
        padding: 20px;
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
    }

    .bracket-legend h4 {
        margin-bottom: 15px;
        color: var(--text-primary);
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
        color: var(--text-primary);
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid var(--border-color);
    }

    .legend-winner {
        background: var(--bracket-winner-bg);
        border-color: var(--color-success);
    }

    .legend-loser {
        background: var(--bracket-loser-bg);
        border-color: var(--color-danger);
    }

    .legend-pending {
        background: var(--alert-warning-bg);
        border-color: var(--color-warning);
    }

    .legend-bye {
        background: var(--bg-hover);
        border-color: var(--color-secondary);
    }

    /* Disputas y estados del match */
    .bracket-match.match-disputed {
        border-color: var(--color-danger);
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--bracket-loser-bg) 100%);
        box-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
    }

    .match-status-bar {
        text-align: center;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed var(--border-color);
    }

    .captain-report {
        background: var(--notification-unread-bg);
        border-radius: 6px;
        padding: 10px;
        margin-top: 10px;
    }

    .admin-actions {
        background: var(--bg-card-secondary);
        border-radius: 6px;
        padding: 10px;
    }

    .admin-actions details {
        margin-bottom: 10px;
    }

    .admin-actions details summary {
        cursor: pointer;
        color: var(--text-primary);
    }

    .dispute-info {
        background: var(--bracket-loser-bg);
        border: 1px solid var(--color-danger);
        border-radius: 4px;
        padding: 8px;
        margin-bottom: 10px;
        font-size: 0.85rem;
        color: var(--text-primary);
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
        color: var(--text-secondary);
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .legend-disputed {
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--bracket-loser-bg) 100%);
        border-color: var(--color-danger);
    }

    /* Página de disputas */
    .disputes-container {
        margin-top: 20px;
    }

    .dispute-card {
        background: var(--bg-card);
        border: 2px solid var(--color-danger);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
    }

    .dispute-card h4 {
        color: var(--color-danger);
        margin-bottom: 15px;
    }

    .dispute-reports {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }

    .dispute-report {
        background: var(--bg-card-secondary);
        border-radius: 6px;
        padding: 12px;
    }

    .dispute-report h5 {
        font-size: 0.9rem;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .dispute-score {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: center;
        color: var(--text-secondary);
    }

    .dispute-actions {
        border-top: 1px solid var(--border-color);
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

        .bracket-match {
            padding: 12px;
        }

        .bracket-team {
            padding: 8px 10px;
        }

        .team-score {
            padding: 3px 8px;
            font-size: 0.85rem;
        }
    }

    /* Ocultar overflow en body cuando menú abierto */
    body.menu-open {
        overflow: hidden;
    }

    /* ==========================================
       CHECKBOX GRID PARA FORMATOS
       ========================================== */
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-top: 8px;
    }

    .checkbox-card {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 12px;
        background: var(--bg-card-secondary);
        border: 2px solid var(--border-color);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
        text-align: center;
    }

    .checkbox-card:hover {
        border-color: var(--color-primary);
        background: var(--bg-hover);
    }

    .checkbox-card:has(input:checked) {
        border-color: var(--color-primary);
        background: rgba(0, 184, 169, 0.1);
    }

    .checkbox-card input[type="checkbox"] {
        width: 18px;
        height: 18px;
        min-width: 18px;
        accent-color: var(--color-primary);
        cursor: pointer;
        margin: 0;
        vertical-align: middle;
    }

    .checkbox-card-label {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.9rem;
        line-height: 18px;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .checkbox-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 480px) {
        .checkbox-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ==========================================
       SELECTORES DE ROL/POSICIÓN
       ========================================== */
    .role-selector-form {
        margin: 0;
    }

    .role-selectors {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
    }

    .role-selectors select {
        min-width: 100px;
        max-width: 130px;
        font-size: 0.8rem;
        padding: 4px 8px;
    }

    .role-selectors .btn-sm {
        padding: 4px 10px;
    }

    .player-roles {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .badge-info {
        background: var(--color-primary);
        color: white;
    }

    @media (max-width: 768px) {
        .role-selectors {
            flex-direction: column;
            align-items: flex-start;
        }

        .role-selectors select {
            max-width: 100%;
            width: 100%;
        }
    }

    /* ==========================================
       RANKINGS & TABS ESTILO CHALLONGE
       ========================================== */
    .ranking-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid var(--border-color);
        border-radius: 50px;
        background: var(--bg-card);
        color: var(--text-secondary);
        transition: all 0.25s ease;
    }

    .tab-btn:hover {
        border-color: var(--color-primary);
        color: var(--color-primary);
        text-decoration: none;
    }

    .tab-btn.active {
        background: var(--color-primary);
        border-color: var(--color-primary);
        color: white;
    }

    .ranking-filters {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: flex-end;
        padding: 20px;
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        margin-bottom: 24px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-group .form-control {
        min-width: 180px;
    }

    .ranking-table .rank-col {
        width: 60px;
        text-align: center;
        font-weight: 700;
    }

    .rank-medal {
        font-size: 1.5rem;
    }

    .ranking-table .top-rank {
        background: rgba(0, 229, 204, 0.05);
    }

    .ranking-table .rank-1 {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.1) 0%, transparent 100%);
    }

    .ranking-table .rank-2 {
        background: linear-gradient(90deg, rgba(192, 192, 192, 0.1) 0%, transparent 100%);
    }

    .ranking-table .rank-3 {
        background: linear-gradient(90deg, rgba(205, 127, 50, 0.1) 0%, transparent 100%);
    }

    [data-theme="dark"] .ranking-table .rank-1 {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.15) 0%, transparent 100%);
    }

    [data-theme="dark"] .ranking-table .rank-2 {
        background: linear-gradient(90deg, rgba(192, 192, 192, 0.12) 0%, transparent 100%);
    }

    [data-theme="dark"] .ranking-table .rank-3 {
        background: linear-gradient(90deg, rgba(205, 127, 50, 0.12) 0%, transparent 100%);
    }

    .streak-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }

    .streak-badge.positive {
        background: rgba(0, 184, 148, 0.15);
        color: var(--color-success);
    }

    .streak-badge.negative {
        background: rgba(231, 76, 60, 0.15);
        color: var(--color-danger);
    }

    @media (max-width: 768px) {
        .ranking-filters {
            flex-direction: column;
        }

        .filter-group .form-control {
            min-width: 100%;
        }
    }
</style>
