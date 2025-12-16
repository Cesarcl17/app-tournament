<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>App Tournament</title>
</head>
<body>

    <h1>LAYOUT APP CARGADO</h1>

    <nav>
        <a href="{{ url('/') }}">Inicio</a> |
        <a href="{{ url('/torneos') }}">Torneos</a>
    </nav>
    @if (session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif


    <hr>

    @if(Auth::check())
        <div style="background:#f4f4f4;padding:10px;margin-bottom:15px;">
            Usuario: <strong>{{ Auth::user()->name }}</strong>
            — Rol global: <strong>{{ Auth::user()->role }}</strong>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;margin-left:15px;">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>
    @else
        <div style="background:#fdecea;padding:10px;margin-bottom:15px;">
            No hay ningún usuario logueado
            <a href="{{ route('login') }}">Login</a>
        </div>
    @endif


    <hr>



    @yield('content')

</body>
</html>
