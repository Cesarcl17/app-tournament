<?php

namespace App\Notifications;

use App\Models\TournamentMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchDisputed extends Notification implements ShouldQueue
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
            ->subject('⚠️ Nueva disputa en ' . $tournament->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Se ha detectado una disputa en una partida del torneo.')
            ->line('**Torneo:** ' . $tournament->name)
            ->line('**Partida:** ' . $this->match->team1->name . ' vs ' . $this->match->team2->name)
            ->line('**Ronda:** ' . $this->match->round)
            ->line('Los capitanes han reportado resultados diferentes y se requiere tu intervención.')
            ->action('Resolver disputa', url('/torneos/' . $tournament->id . '/disputas'))
            ->line('Por favor, revisa las evidencias y establece el resultado oficial.');
    }

    public function toDatabase(object $notifiable): array
    {
        $tournament = $this->match->tournament;

        return [
            'type' => 'match_disputed',
            'icon' => '⚠️',
            'title' => 'Nueva disputa',
            'message' => "Disputa en {$this->match->team1->name} vs {$this->match->team2->name}",
            'details' => "Los capitanes han reportado resultados diferentes en el torneo {$tournament->name}",
            'tournament_id' => $tournament->id,
            'match_id' => $this->match->id,
            'action_url' => '/torneos/' . $tournament->id . '/disputas',
            'action_text' => 'Resolver disputa',
        ];
    }
}
