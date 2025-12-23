<?php

namespace App\Notifications;

use App\Models\TournamentMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TournamentMatch $match
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tournament = $this->match->tournament;

        return (new MailMessage)
            ->subject('📅 Partida programada - ' . $tournament->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Se ha programado una partida en la que participa tu equipo.')
            ->line('**Torneo:** ' . $tournament->name)
            ->line('**Partida:** ' . $this->match->team1->name . ' vs ' . $this->match->team2->name)
            ->line('**Fecha y hora:** ' . $this->match->scheduled_at->format('d/m/Y H:i'))
            ->line('**Ronda:** ' . $this->match->round)
            ->action('Ver bracket', url('/torneos/' . $tournament->id . '/bracket'))
            ->line('¡No olvides estar preparado para la partida!');
    }

    public function toDatabase(object $notifiable): array
    {
        $tournament = $this->match->tournament;

        return [
            'type' => 'match_scheduled',
            'icon' => '📅',
            'title' => 'Partida programada',
            'message' => "{$this->match->team1->name} vs {$this->match->team2->name}",
            'details' => "Fecha: {$this->match->scheduled_at->format('d/m/Y H:i')} en {$tournament->name}",
            'tournament_id' => $tournament->id,
            'match_id' => $this->match->id,
            'scheduled_at' => $this->match->scheduled_at->toIso8601String(),
            'action_url' => '/torneos/' . $tournament->id . '/bracket',
            'action_text' => 'Ver bracket',
        ];
    }
}
