{{-- 
    Esta vista no se usa - la ruta '/' usa HomeController que renderiza inicio.blade.php
    Se mantiene por compatibilidad pero redirige al inicio
--}}
@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <script>window.location.href = "{{ route('home') }}";</script>
@endsection
