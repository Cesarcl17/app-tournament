<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'The Tournament Series - Plataforma de torneos de eSports. Crea equipos, compite en torneos y demuestra tu habilidad.')">
    <meta name="keywords" content="esports, torneos, gaming, competitivo, equipos, league of legends, valorant, cs2, the tournament series, tts">
    <meta name="author" content="The Tournament Series">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'The Tournament Series')">
    <meta property="og:description" content="@yield('meta_description', 'The Tournament Series - Plataforma de torneos de eSports.')">
    
    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'The Tournament Series')">
    <meta name="twitter:description" content="@yield('meta_description', 'The Tournament Series - Plataforma de torneos de eSports.')">
    
    <title>@yield('title', 'The Tournament Series')</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='l' x1='0%25' y1='0%25' x2='100%25' y2='0%25'><stop offset='0%25' stop-color='%2300e5cc'/><stop offset='100%25' stop-color='%2300b8a9'/></linearGradient></defs><circle cx='50' cy='50' r='48' fill='%23121212'/><g fill='url(%23l)' transform='translate(15,20)'><ellipse cx='8' cy='8' rx='4' ry='8' transform='rotate(-30 8 8)'/><ellipse cx='5' cy='18' rx='4' ry='8' transform='rotate(-50 5 18)'/><ellipse cx='4' cy='30' rx='4' ry='8' transform='rotate(-70 4 30)'/><ellipse cx='5' cy='42' rx='4' ry='7' transform='rotate(-85 5 42)'/><ellipse cx='8' cy='52' rx='4' ry='6' transform='rotate(-95 8 52)'/></g><g fill='url(%23l)' transform='translate(85,20) scale(-1,1)'><ellipse cx='8' cy='8' rx='4' ry='8' transform='rotate(-30 8 8)'/><ellipse cx='5' cy='18' rx='4' ry='8' transform='rotate(-50 5 18)'/><ellipse cx='4' cy='30' rx='4' ry='8' transform='rotate(-70 4 30)'/><ellipse cx='5' cy='42' rx='4' ry='7' transform='rotate(-85 5 42)'/><ellipse cx='8' cy='52' rx='4' ry='6' transform='rotate(-95 8 52)'/></g><text x='50' y='58' text-anchor='middle' font-family='Arial' font-size='28' font-weight='900' fill='white'>TTS</text></svg>">
    
    @include('partials.styles')
    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <div class="navbar-left">
            {{-- Logo con corona de laurel integrada --}}
            <a href="{{ url('/') }}" class="navbar-brand">
                <div class="brand-laurel-wrapper">
                    {{-- Laurel izquierdo --}}
                    <svg class="laurel-left" viewBox="0 0 20 50" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="laurelL" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#00e5cc"/>
                                <stop offset="100%" style="stop-color:#00b8a9"/>
                            </linearGradient>
                        </defs>
                        <g fill="url(#laurelL)">
                            <ellipse cx="12" cy="6" rx="3" ry="6" transform="rotate(30 12 6)"/>
                            <ellipse cx="10" cy="15" rx="3" ry="6" transform="rotate(45 10 15)"/>
                            <ellipse cx="8" cy="25" rx="3" ry="6" transform="rotate(60 8 25)"/>
                            <ellipse cx="8" cy="35" rx="3" ry="5" transform="rotate(75 8 35)"/>
                            <ellipse cx="10" cy="44" rx="3" ry="5" transform="rotate(85 10 44)"/>
                        </g>
                    </svg>
                    
                    {{-- Texto --}}
                    <span class="brand-text">
                        <span class="brand-text-top">THE TOURNAMENT</span>
                        <span class="brand-text-bottom">SERIES</span>
                    </span>
                    
                    {{-- Laurel derecho --}}
                    <svg class="laurel-right" viewBox="0 0 20 50" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="laurelR" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#00b8a9"/>
                                <stop offset="100%" style="stop-color:#00e5cc"/>
                            </linearGradient>
                        </defs>
                        <g fill="url(#laurelR)">
                            <ellipse cx="8" cy="6" rx="3" ry="6" transform="rotate(-30 8 6)"/>
                            <ellipse cx="10" cy="15" rx="3" ry="6" transform="rotate(-45 10 15)"/>
                            <ellipse cx="12" cy="25" rx="3" ry="6" transform="rotate(-60 12 25)"/>
                            <ellipse cx="12" cy="35" rx="3" ry="5" transform="rotate(-75 12 35)"/>
                            <ellipse cx="10" cy="44" rx="3" ry="5" transform="rotate(-85 10 44)"/>
                        </g>
                    </svg>
                </div>
                
                {{-- Versión móvil --}}
                <div class="brand-laurel-wrapper-mobile show-mobile">
                    <svg class="laurel-left-sm" viewBox="0 0 20 50" xmlns="http://www.w3.org/2000/svg">
                        <g fill="#00e5cc">
                            <ellipse cx="12" cy="8" rx="3" ry="6" transform="rotate(35 12 8)"/>
                            <ellipse cx="9" cy="18" rx="3" ry="6" transform="rotate(55 9 18)"/>
                            <ellipse cx="8" cy="30" rx="3" ry="6" transform="rotate(75 8 30)"/>
                            <ellipse cx="10" cy="42" rx="3" ry="5" transform="rotate(88 10 42)"/>
                        </g>
                    </svg>
                    <span class="brand-text-short">TTS</span>
                    <svg class="laurel-right-sm" viewBox="0 0 20 50" xmlns="http://www.w3.org/2000/svg">
                        <g fill="#00e5cc">
                            <ellipse cx="8" cy="8" rx="3" ry="6" transform="rotate(-35 8 8)"/>
                            <ellipse cx="11" cy="18" rx="3" ry="6" transform="rotate(-55 11 18)"/>
                            <ellipse cx="12" cy="30" rx="3" ry="6" transform="rotate(-75 12 30)"/>
                            <ellipse cx="10" cy="42" rx="3" ry="5" transform="rotate(-88 10 42)"/>
                        </g>
                    </svg>
                </div>
            </a>
            
            <a href="{{ url('/') }}">🏠 Inicio</a>
            <a href="{{ route('torneos.index') }}">⚔️ Torneos</a>
            <a href="{{ route('rankings.index') }}">🏆 Rankings</a>
            <a href="{{ route('calendario.index') }}">📅 Calendario</a>
            <a href="{{ route('activities.index') }}">📢 Actividad</a>
        </div>
        <div class="navbar-right">
            {{-- Toggle de tema oscuro/claro --}}
            <button class="theme-toggle" id="themeToggle" title="Cambiar tema">
                <span id="themeIcon">☀️</span>
            </button>

            @if(Auth::check())
                {{-- Botones Admin --}}
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.games.index') }}" class="navbar-icon-btn" title="Gestión de Juegos">
                        🕹️
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="navbar-icon-btn" title="Dashboard Admin">
                        📊
                    </a>
                @endif

                {{-- Sistema de notificaciones --}}
                @php
                    $unreadCount = Auth::user()->unreadNotifications->count();
                    $notifications = Auth::user()->notifications()->take(5)->get();
                @endphp
                <div class="notifications-dropdown">
                    <button class="notifications-trigger" onclick="toggleNotifications()">
                        🔔
                        @if($unreadCount > 0)
                            <span class="notifications-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="notifications-menu" id="notificationsMenu">
                        <div class="notifications-header">
                            <span>Notificaciones</span>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-link">Marcar todas</button>
                                </form>
                            @endif
                        </div>
                        <div class="notifications-list">
                            @forelse($notifications as $notification)
                                <a href="{{ route('notifications.show', $notification->id) }}"
                                   class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                                    <span class="notification-icon">{{ $notification->data['icon'] ?? '📢' }}</span>
                                    <div class="notification-content">
                                        <p class="notification-message">{{ $notification->data['message'] ?? 'Nueva notificación' }}</p>
                                        <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <div class="notification-empty">
                                    No tienes notificaciones
                                </div>
                            @endforelse
                        </div>
                        <div class="notifications-footer">
                            <a href="{{ route('notifications.index') }}">Ver todas las notificaciones</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="user-info">
                    <strong>{{ Auth::user()->name }}</strong>
                    <span class="badge badge-primary">{{ Auth::user()->role }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">Cerrar sesión</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn btn-success btn-sm">Registrarse</a>
            @endif
        </div>
    </nav>

    @include('partials.alerts')

    <main>
        @yield('content')
    </main>

    <script>
        function toggleNotifications() {
            const menu = document.getElementById('notificationsMenu');
            menu.classList.toggle('show');
        }

        // Cerrar al hacer click fuera
        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.notifications-dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                document.getElementById('notificationsMenu')?.classList.remove('show');
            }
        });

        // Sistema de tema oscuro/claro
        (function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;

            // Cargar tema guardado o usar dark por defecto
            const savedTheme = localStorage.getItem('theme');
            const currentTheme = savedTheme || 'dark';

            html.setAttribute('data-theme', currentTheme);
            themeIcon.textContent = currentTheme === 'dark' ? '☀️' : '🌙';

            // Toggle del tema
            themeToggle.addEventListener('click', function() {
                const isDark = html.getAttribute('data-theme') === 'dark';
                const newTheme = isDark ? 'light' : 'dark';

                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                themeIcon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
            });
        })();
    </script>

    @stack('scripts')

</body>
</html>
