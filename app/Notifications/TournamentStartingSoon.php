<?php

namespace App\Notifications;

use App\Models\Tournament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TournamentStartingSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Tournament $tournament,
        public int $hoursUntilStart
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $timeText = $this->hoursUntilStart >= 24 
            ? floor($this->hoursUntilStart / 24) . ' día(s)'
            : $this->hoursUntilStart . ' hora(s)';
        
        return (new MailMessage)
            ->subject("🎮 ¡{$this->tournament->name} empieza en {$timeText}!")
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line("El torneo en el que estás inscrito comienza pronto.")
            ->line('**Torneo:** ' . $this->tournament->name)
            ->line('**Juego:** ' . $this->tournament->game->name)
            ->line('**Comienza en:** ' . $timeText)
            ->line('**Fecha de inicio:** ' . $this->tournament->start_date->format('d/m/Y H:i'))
            ->action('Ver torneo', url('/torneos/' . $this->tournament->id))
            ->line('¡Prepárate para competir!');
    }

    public function toDatabase(object $notifiable): array
    {
        $timeText = $this->hoursUntilStart >= 24 
            ? floor($this->hoursUntilStart / 24) . ' día(s)'
            : $this->hoursUntilStart . ' hora(s)';
        
        return [
            'type' => 'tournament_starting_soon',
            'icon' => '🎮',
            'title' => '¡Torneo próximo!',
            'message' => "{$this->tournament->name} empieza en {$timeText}",
            'details' => "Fecha: {$this->tournament->start_date->format('d/m/Y H:i')}",
            'tournament_id' => $this->tournament->id,
            'action_url' => '/torneos/' . $this->tournament->id,
            'action_text' => 'Ver torneo',
        ];
    }
}
