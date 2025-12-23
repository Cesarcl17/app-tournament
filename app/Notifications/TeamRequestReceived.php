<?php

namespace App\Notifications;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Team $team,
        public User $requester
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('👥 Nueva solicitud de unión - ' . $this->team->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Has recibido una solicitud para unirse a tu equipo.')
            ->line('**Equipo:** ' . $this->team->name)
            ->line('**Solicitante:** ' . $this->requester->name)
            ->line('**Email:** ' . $this->requester->email)
            ->action('Ver solicitudes', url('/teams/' . $this->team->id))
            ->line('Puedes aceptar o rechazar la solicitud desde la página del equipo.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'team_request_received',
            'icon' => '👥',
            'title' => 'Solicitud de unión',
            'message' => "{$this->requester->name} quiere unirse a {$this->team->name}",
            'details' => 'Revisa la solicitud y decide si aceptarla o rechazarla',
            'team_id' => $this->team->id,
            'requester_id' => $this->requester->id,
            'action_url' => '/teams/' . $this->team->id,
            'action_text' => 'Ver equipo',
        ];
    }
}
