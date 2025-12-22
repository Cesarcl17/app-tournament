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

</body>
</html>
