@extends('layouts.app')

@section('title', 'Notificación')

@section('content')
    <div class="page-header">
        <h1>🔔 Notificación</h1>
        <a href="{{ route('notifications.index') }}" class="btn btn-secondary">Volver a notificaciones</a>
    </div>

    <div class="notification-detail-card">
        <div class="notification-detail-header">
            <span class="notification-detail-icon">{{ $notification->data['icon'] ?? '📢' }}</span>
            <span class="notification-detail-type">{{ $notification->data['type'] ?? 'Notificación' }}</span>
            <span class="notification-detail-time">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
        </div>

        <div class="notification-detail-body">
            <h3>{{ $notification->data['title'] ?? $notification->data['message'] ?? 'Sin título' }}</h3>

            @if(isset($notification->data['message']))
                <p>{{ $notification->data['message'] }}</p>
            @endif

            @if(isset($notification->data['details']))
                <p class="text-muted">{{ $notification->data['details'] }}</p>
            @endif
        </div>

        <div class="notification-detail-footer">
            @if(isset($notification->data['action_url']) && isset($notification->data['action_text']))
                <a href="{{ $notification->data['action_url'] }}" class="btn btn-primary">
                    {{ $notification->data['action_text'] }}
                </a>
            @endif

            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar notificación</button>
            </form>
        </div>
    </div>
@endsection
