<?php

namespace App\Notifications;

use App\Models\Tournament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BracketGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Tournament $tournament
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🏆 ¡Bracket generado! - ' . $this->tournament->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El bracket del torneo ha sido generado.')
            ->line('**Torneo:** ' . $this->tournament->name)
            ->line('**Juego:** ' . $this->tournament->game->name)
            ->line('Ya puedes ver los emparejamientos y prepararte para tus partidas.')
            ->action('Ver bracket', url('/torneos/' . $this->tournament->id . '/bracket'))
            ->line('¡Buena suerte en el torneo!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'bracket_generated',
            'icon' => '🏆',
            'title' => 'Bracket generado',
            'message' => "El bracket de {$this->tournament->name} está listo",
            'details' => 'Ya puedes ver los emparejamientos del torneo',
            'tournament_id' => $this->tournament->id,
            'action_url' => '/torneos/' . $this->tournament->id . '/bracket',
            'action_text' => 'Ver bracket',
        ];
    }
}
