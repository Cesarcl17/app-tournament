{{-- Mensajes de éxito --}}
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Mensajes de error --}}
@if (session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

{{-- Mensajes de advertencia --}}
@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

{{-- Errores de validación --}}
@if ($errors->any())
    <div class="alert alert-error">
        <ul class="error-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
