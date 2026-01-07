<nav class="navbar">
    <div class="nav-container">
        <a href="{{ url('/') }}" class="nav-brand">
            <span class="nav-logo">🏆</span>
            <span class="nav-brand-text">Tournament App</span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Menú">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <div class="nav-links" id="navLinks">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                <i class="nav-icon">🏠</i>
                <span>Inicio</span>
            </a>
            <a href="{{ url('/torneos') }}" class="{{ request()->is('torneos*') && !request()->is('torneos/create') ? 'active' : '' }}">
                <i class="nav-icon">🎮</i>
                <span>Torneos</span>
            </a>
            @auth
                <a href="{{ url('/teams') }}" class="{{ request()->is('teams*') ? 'active' : '' }}">
                    <i class="nav-icon">👥</i>
                    <span>Equipos</span>
                </a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.games.index') }}" class="{{ request()->is('admin/games*') ? 'active' : '' }}">
                        <i class="nav-icon">🎯</i>
                        <span>Juegos</span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="nav-icon">📊</i>
                        <span>Dashboard</span>
                    </a>
                @endif
            @endauth
        </div>

        <div class="nav-actions">
            @auth
                <div class="nav-user">
                    <span class="nav-user-name">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">
                            Salir
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Registrarse</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navToggle = document.getElementById('navToggle');
        const navLinks = document.getElementById('navLinks');

        if (navToggle && navLinks) {
            navToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                navToggle.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            });

            // Cerrar menú al hacer click en un enlace
            navLinks.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.classList.remove('active');
                    navToggle.classList.remove('active');
                    document.body.classList.remove('menu-open');
                });
            });

            // Cerrar menú al hacer click fuera
            document.addEventListener('click', function(e) {
                if (!navToggle.contains(e.target) && !navLinks.contains(e.target)) {
                    navLinks.classList.remove('active');
                    navToggle.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        }
    });
</script>
