@extends('layouts.app')

@section('content')
    <h1>Iniciar sesión</h1>

    @if ($errors->any())
        <div style="color:red">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div>
            <label>Email</label><br>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>Contraseña</label><br>
            <input type="password" name="password" required>
        </div>

        <br>

        <button type="submit">Entrar</button>
    </form>
@endsection
