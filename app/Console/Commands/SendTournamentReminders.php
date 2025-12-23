<?php

namespace App\Console\Commands;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\User;
use App\Notifications\MatchStartingSoon;
use App\Notifications\TournamentStartingSoon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendTournamentReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tournaments:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Envía recordatorios de torneos y partidas próximas a comenzar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Enviando recordatorios de torneos...');
        
        // Recordatorios de torneos: 24h y 1h antes
        $this->sendTournamentReminders();
        
        // Recordatorios de partidas: 1h y 15min antes
        $this->sendMatchReminders();
        
        $this->info('✅ Recordatorios enviados correctamente.');
        
        return Command::SUCCESS;
    }

    /**
     * Envía recordatorios de torneos próximos a comenzar
     */
    private function sendTournamentReminders(): void
    {
        $now = Carbon::now();
        
        // Torneos que empiezan en ~24 horas (entre 23h50m y 24h10m para dar margen)
        $tournamentsIn24h = Tournament::whereBetween('start_date', [
            $now->copy()->addHours(23)->addMinutes(50),
            $now->copy()->addHours(24)->addMinutes(10),
        ])->get();

        foreach ($tournamentsIn24h as $tournament) {
            $this->notifyTournamentParticipants($tournament, 24);
        }
        
        // Torneos que empiezan en ~1 hora (entre 50min y 70min)
        $tournamentsIn1h = Tournament::whereBetween('start_date', [
            $now->copy()->addMinutes(50),
            $now->copy()->addMinutes(70),
        ])->get();

        foreach ($tournamentsIn1h as $tournament) {
            $this->notifyTournamentParticipants($tournament, 1);
        }
        
        $this->info("Torneos en 24h: {$tournamentsIn24h->count()}, en 1h: {$tournamentsIn1h->count()}");
    }

    /**
     * Envía recordatorios de partidas próximas a comenzar
     */
    private function sendMatchReminders(): void
    {
        $now = Carbon::now();
        
        // Partidas que empiezan en ~1 hora
        $matchesIn1h = TournamentMatch::whereNotNull('scheduled_at')
            ->whereNull('winner_id')
            ->whereNotNull('team1_id')
            ->whereNotNull('team2_id')
            ->where('is_bye', false)
            ->whereBetween('scheduled_at', [
                $now->copy()->addMinutes(55),
                $now->copy()->addMinutes(65),
            ])
            ->get();

        foreach ($matchesIn1h as $match) {
            $this->notifyMatchParticipants($match, 60);
        }
        
        // Partidas que empiezan en ~15 minutos
        $matchesIn15m = TournamentMatch::whereNotNull('scheduled_at')
            ->whereNull('winner_id')
            ->whereNotNull('team1_id')
            ->whereNotNull('team2_id')
            ->where('is_bye', false)
            ->whereBetween('scheduled_at', [
                $now->copy()->addMinutes(13),
                $now->copy()->addMinutes(17),
            ])
            ->get();

        foreach ($matchesIn15m as $match) {
            $this->notifyMatchParticipants($match, 15);
        }
        
        $this->info("Partidas en 1h: {$matchesIn1h->count()}, en 15min: {$matchesIn15m->count()}");
    }

    /**
     * Notifica a todos los participantes de un torneo
     */
    private function notifyTournamentParticipants(Tournament $tournament, int $hours): void
    {
        // Obtener todos los usuarios de los equipos inscritos
        $userIds = DB::table('team_user')
            ->join('tournament_user', 'team_user.team_id', '=', 'tournament_user.team_id')
            ->where('tournament_user.tournament_id', $tournament->id)
            ->pluck('team_user.user_id')
            ->unique();

        $users = User::whereIn('id', $userIds)->get();
        
        foreach ($users as $user) {
            // Evitar duplicados: verificar si ya se notificó
            $alreadyNotified = $user->notifications()
                ->where('type', TournamentStartingSoon::class)
                ->whereRaw("JSON_EXTRACT(data, '$.tournament_id') = ?", [$tournament->id])
                ->where('created_at', '>=', Carbon::now()->subHours(2))
                ->exists();
            
            if (!$alreadyNotified) {
                $user->notify(new TournamentStartingSoon($tournament, $hours));
            }
        }
        
        $this->line("  - {$tournament->name}: {$users->count()} usuarios notificados ({$hours}h)");
    }

    /**
     * Notifica a los participantes de una partida
     */
    private function notifyMatchParticipants(TournamentMatch $match, int $minutes): void
    {
        // Obtener usuarios de ambos equipos
        $team1Users = $match->team1->users;
        $team2Users = $match->team2->users;
        $allUsers = $team1Users->merge($team2Users);
        
        foreach ($allUsers as $user) {
            // Evitar duplicados
            $alreadyNotified = $user->notifications()
                ->where('type', MatchStartingSoon::class)
                ->whereRaw("JSON_EXTRACT(data, '$.match_id') = ?", [$match->id])
                ->where('created_at', '>=', Carbon::now()->subMinutes(30))
                ->exists();
            
            if (!$alreadyNotified) {
                $user->notify(new MatchStartingSoon($match, $minutes));
            }
        }
        
        $this->line("  - Partida #{$match->id}: {$allUsers->count()} usuarios notificados ({$minutes}min)");
    }
}
