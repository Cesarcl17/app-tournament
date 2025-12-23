<?php

namespace App\Notifications;

use App\Models\TournamentMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchResultReported extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TournamentMatch $match,
        public string $reporterTeamName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tournament = $this->match->tournament;
        
        return (new MailMessage)
            ->subject('📝 Resultado reportado - ' . $tournament->name)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El capitán del equipo contrario ha reportado el resultado de la partida.')
            ->line('**Torneo:** ' . $tournament->name)
            ->line('**Partida:** ' . $this->match->team1->name . ' vs ' . $this->match->team2->name)
            ->line('**Reportado por:** ' . $this->reporterTeamName)
            ->line('Ahora debes reportar el resultado desde tu perspectiva.')
            ->line('Si ambos resultados coinciden, la partida se validará automáticamente.')
            ->action('Reportar resultado', url('/torneos/' . $tournament->id . '/bracket'))
            ->line('Por favor, reporta el resultado lo antes posible.');
    }

    public function toDatabase(object $notifiable): array
    {
        $tournament = $this->match->tournament;
        
        return [
            'type' => 'match_result_reported',
            'icon' => '📝',
            'title' => 'Reporta tu resultado',
            'message' => "{$this->reporterTeamName} ha reportado el resultado",
            'details' => "Reporta tu resultado en {$this->match->team1->name} vs {$this->match->team2->name}",
            'tournament_id' => $tournament->id,
            'match_id' => $this->match->id,
            'action_url' => '/torneos/' . $tournament->id . '/bracket',
            'action_text' => 'Reportar resultado',
        ];
    }
}
