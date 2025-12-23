<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamRequestApproved extends Notification implements ShouldQueue
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
            ->subject('✅ ¡Bienvenido a ' . $this->team->name . '!')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('¡Tu solicitud para unirte al equipo ha sido aceptada!')
            ->line('**Equipo:** ' . $this->team->name)
            ->line('Ahora formas parte del equipo y podrás participar en los torneos.')
            ->action('Ver mi equipo', url('/teams/' . $this->team->id))
            ->line('¡Buena suerte en tus competiciones!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'team_request_approved',
            'icon' => '✅',
            'title' => '¡Solicitud aceptada!',
            'message' => "Tu solicitud para unirte a {$this->team->name} fue aceptada",
            'details' => 'Ya formas parte del equipo',
            'team_id' => $this->team->id,
            'action_url' => '/teams/' . $this->team->id,
            'action_text' => 'Ver equipo',
        ];
    }
}
