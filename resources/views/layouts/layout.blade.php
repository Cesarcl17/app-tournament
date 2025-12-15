<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>App Tournament</title>
</head>
<body>

    <h1>App Tournament</h1>

    <hr>

    <nav>
        <a href="{{ url('/') }}">Inicio</a> |
        <a href="{{ url('/torneos') }}">Torneos</a>
    </nav>

    <hr>

    @yield('content')

</body>
</html>
