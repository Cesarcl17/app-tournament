<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Team $team
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('❌ Solicitud rechazada - ' . $this->team->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Tu solicitud para unirte al equipo ha sido rechazada.')
            ->line('**Equipo:** ' . $this->team->name)
            ->line('Puedes buscar otros equipos o crear tu propio equipo.')
            ->action('Ver equipos', url('/teams'))
            ->line('¡No te desanimes, hay muchos equipos buscando jugadores!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'team_request_rejected',
            'icon' => '❌',
            'title' => 'Solicitud rechazada',
            'message' => "Tu solicitud para unirte a {$this->team->name} fue rechazada",
            'details' => 'Puedes buscar otros equipos o crear el tuyo propio',
            'team_id' => $this->team->id,
            'action_url' => '/teams',
            'action_text' => 'Ver equipos',
        ];
    }
}
