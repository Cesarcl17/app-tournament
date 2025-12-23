@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
    <div class="page-header">
        <h1>🔔 Notificaciones</h1>
        <div class="actions-inline">
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Marcar todas como leídas</button>
                </form>
            @endif
            @if(auth()->user()->readNotifications()->count() > 0)
                <form action="{{ route('notifications.destroyRead') }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar todas las notificaciones leídas?')">
                        Eliminar leídas
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Filtros --}}
    <div class="notifications-filters">
        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
           class="btn {{ $filter === 'all' ? 'btn-primary' : 'btn-secondary' }}">
            Todas
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
           class="btn {{ $filter === 'unread' ? 'btn-primary' : 'btn-secondary' }}">
            No leídas ({{ auth()->user()->unreadNotifications->count() }})
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
           class="btn {{ $filter === 'read' ? 'btn-primary' : 'btn-secondary' }}">
            Leídas
        </a>
    </div>

    <div class="notifications-page-list">
        @forelse($notifications as $notification)
            <div class="notification-card {{ $notification->read_at ? 'read' : 'unread' }}">
                <div class="notification-card-icon">
                    {{ $notification->data['icon'] ?? '📢' }}
                </div>
                <div class="notification-card-content">
                    <p class="notification-card-message">
                        {{ $notification->data['message'] ?? 'Nueva notificación' }}
                    </p>
                    @if(isset($notification->data['details']))
                        <p class="notification-card-details">
                            {{ $notification->data['details'] }}
                        </p>
                    @endif
                    <small class="notification-card-time">
                        {{ $notification->created_at->format('d/m/Y H:i') }} 
                        ({{ $notification->created_at->diffForHumans() }})
                    </small>
                </div>
                <div class="notification-card-actions">
                    @if(isset($notification->data['action_url']))
                        <a href="{{ route('notifications.show', $notification->id) }}" class="btn btn-sm btn-primary">
                            Ver
                        </a>
                    @endif
                    @if(!$notification->read_at)
                        <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-secondary">✓</button>
                        </form>
                    @endif
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">✕</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                No tienes notificaciones {{ $filter === 'unread' ? 'sin leer' : ($filter === 'read' ? 'leídas' : '') }}.
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div class="pagination-container">
        {{ $notifications->appends(['filter' => $filter])->links() }}
    </div>
@endsection
