<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lista todas las notificaciones del usuario
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $filter = $request->get('filter', 'all');
        
        if ($filter === 'unread') {
            $notifications = $user->unreadNotifications()->paginate(15);
        } elseif ($filter === 'read') {
            $notifications = $user->readNotifications()->paginate(15);
        } else {
            $notifications = $user->notifications()->paginate(15);
        }
        
        return view('notifications.index', compact('notifications', 'filter'));
    }

    /**
     * Muestra una notificación y la marca como leída
     */
    public function show(string $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        // Marcar como leída
        if (!$notification->read_at) {
            $notification->markAsRead();
        }
        
        // Redirigir a la URL de acción si existe
        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }
        
        // Si no hay URL, mostrar página de notificación
        return view('notifications.show', compact('notification'));
    }

    /**
     * Marca una notificación como leída
     */
    public function markAsRead(string $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return back()->with('success', 'Notificación marcada como leída');
    }

    /**
     * Marca todas las notificaciones como leídas
     */
    public function markAllAsRead()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    /**
     * Elimina una notificación
     */
    public function destroy(string $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();
        
        return back()->with('success', 'Notificación eliminada');
    }

    /**
     * Elimina todas las notificaciones leídas
     */
    public function destroyRead()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->readNotifications()->delete();
        
        return back()->with('success', 'Notificaciones leídas eliminadas');
    }
}
