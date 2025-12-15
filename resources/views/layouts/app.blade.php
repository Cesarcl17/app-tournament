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

    @yield('content')

</body>
</html>
