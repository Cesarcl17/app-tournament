<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'App Tournament')</title>
    @include('partials.styles')
</head>
<body>

    <nav class="navbar">
        <div class="navbar-left">
            <a href="{{ url('/') }}">Inicio</a>
            <a href="{{ route('torneos.index') }}">Torneos</a>
        </div>
        <div class="navbar-right">
            @if(Auth::check())
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
    </script>

</body>
</html>
