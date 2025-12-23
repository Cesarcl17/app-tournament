<?php

namespace App\Notifications;

use App\Models\TournamentMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchStartingSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TournamentMatch $match,
        public int $minutesUntilStart
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tournament = $this->match->tournament;

        return (new MailMessage)
            ->subject("⏰ ¡Tu partida empieza en {$this->minutesUntilStart} minutos!")
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line("Tu partida está a punto de comenzar en {$this->minutesUntilStart} minutos.")
            ->line('**Torneo:** ' . $tournament->name)
            ->line('**Partida:** ' . $this->match->team1->name . ' vs ' . $this->match->team2->name)
            ->line('**Hora:** ' . $this->match->scheduled_at->format('H:i'))
            ->action('Ver bracket', url('/torneos/' . $tournament->id . '/bracket'))
            ->line('¡Prepárate y buena suerte!');
    }

    public function toDatabase(object $notifiable): array
    {
        $tournament = $this->match->tournament;

        return [
            'type' => 'match_starting_soon',
            'icon' => '⏰',
            'title' => '¡Partida próxima!',
            'message' => "Tu partida empieza en {$this->minutesUntilStart} minutos",
            'details' => "{$this->match->team1->name} vs {$this->match->team2->name} a las {$this->match->scheduled_at->format('H:i')}",
            'tournament_id' => $tournament->id,
            'match_id' => $this->match->id,
            'action_url' => '/torneos/' . $tournament->id . '/bracket',
            'action_text' => 'Ver bracket',
        ];
    }
}
