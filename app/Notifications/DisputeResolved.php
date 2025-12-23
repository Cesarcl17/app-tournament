<?php

namespace App\Notifications;

use App\Models\TournamentMatch;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeResolved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TournamentMatch $match,
        public Team $winner
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tournament = $this->match->tournament;
        $isWinner = $notifiable->teams->contains($this->winner->id);

        return (new MailMessage)
            ->subject('✅ Disputa resuelta - ' . $tournament->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('La disputa de tu partida ha sido resuelta por un administrador.')
            ->line('**Torneo:** ' . $tournament->name)
            ->line('**Partida:** ' . $this->match->team1->name . ' vs ' . $this->match->team2->name)
            ->line('**Resultado oficial:** ' . $this->match->score_team1 . ' - ' . $this->match->score_team2)
            ->line('**Ganador:** ' . $this->winner->name)
            ->line($isWinner ? '¡Felicidades! Tu equipo avanza a la siguiente ronda.' : 'Tu equipo ha sido eliminado del torneo.')
            ->action('Ver bracket', url('/torneos/' . $tournament->id . '/bracket'));
    }

    public function toDatabase(object $notifiable): array
    {
        $tournament = $this->match->tournament;

        return [
            'type' => 'dispute_resolved',
            'icon' => '✅',
            'title' => 'Disputa resuelta',
            'message' => "La disputa de {$this->match->team1->name} vs {$this->match->team2->name} ha sido resuelta",
            'details' => "Ganador: {$this->winner->name} ({$this->match->score_team1} - {$this->match->score_team2})",
            'tournament_id' => $tournament->id,
            'match_id' => $this->match->id,
            'winner_id' => $this->winner->id,
            'action_url' => '/torneos/' . $tournament->id . '/bracket',
            'action_text' => 'Ver bracket',
        ];
    }
}
