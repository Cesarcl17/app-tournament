<?php

namespace App\Notifications;

use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TeamInvitation $invitation
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $team = $this->invitation->team;
        $inviter = $this->invitation->inviter;
        $tournament = $team->tournament;

        $mail = (new MailMessage)
            ->subject("Invitación a unirse al equipo {$team->name}")
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line("{$inviter->name} te ha invitado a unirte al equipo \"{$team->name}\" en el torneo \"{$tournament->name}\".");

        if ($this->invitation->message) {
            $mail->line("**Mensaje:** {$this->invitation->message}");
        }

        $mail->action('Ver invitación', $this->invitation->getAcceptUrl())
             ->line('Esta invitación expira el ' . $this->invitation->expires_at->format('d/m/Y H:i') . '.')
             ->line('¡Nos vemos en el torneo!');

        return $mail;
    }

    public function toArray($notifiable): array
    {
        $team = $this->invitation->team;
        $inviter = $this->invitation->inviter;

        return [
            'type' => 'team_invitation',
            'icon' => '✉️',
            'message' => "{$inviter->name} te invita a unirte al equipo \"{$team->name}\"",
            'invitation_id' => $this->invitation->id,
            'team_id' => $team->id,
            'team_name' => $team->name,
            'tournament_id' => $team->tournament_id,
            'tournament_name' => $team->tournament->name,
            'url' => $this->invitation->getAcceptUrl(),
        ];
    }
}
