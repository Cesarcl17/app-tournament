<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TournamentsWithTeamsSeeder extends Seeder
{
    /**
     * Crear torneos con equipos listos para generar brackets
     * - 2 torneos por cada juego
     * - Cada torneo con 8 equipos (para brackets perfectos)
     * - No genera el bracket (eso lo hace el usuario)
     */
    public function run(): void
    {
        $games = Game::all();

        if ($games->isEmpty()) {
            $this->command->error('No hay juegos. Ejecuta primero: php artisan db:seed --class=GamesSeeder');
            return;
        }

        $tournamentNames = [
            'Copa Invernal 2025',
            'Liga Relámpago',
            'Torneo Navidad',
            'Campeonato Express',
        ];

        $teamPrefixes = [
            ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Eta', 'Theta'],
            ['Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Purple', 'Black', 'White'],
            ['Dragon', 'Phoenix', 'Tiger', 'Wolf', 'Eagle', 'Shark', 'Lion', 'Bear'],
            ['Storm', 'Thunder', 'Fire', 'Ice', 'Shadow', 'Light', 'Void', 'Chaos'],
        ];

        $tournamentIndex = 0;
        $userCounter = 1000; // Empezar desde 1000 para no colisionar con otros seeders

        foreach ($games as $game) {
            $this->command->info("Creando torneos para: {$game->name}");

            // 2 torneos por juego
            for ($t = 0; $t < 2; $t++) {
                $tournamentName = $tournamentNames[$tournamentIndex % count($tournamentNames)] . ' - ' . $game->short_name;

                $tournament = Tournament::create([
                    'name' => $tournamentName,
                    'description' => "Torneo de {$game->name} con 8 equipos listos para competir",
                    'game_id' => $game->id,
                    'team_size' => $game->team_size,
                    'start_date' => now()->addDays(rand(1, 14)),
                    'end_date' => now()->addDays(rand(15, 30)),
                ]);

                $this->command->info("  ✓ Torneo: {$tournamentName}");

                // 8 equipos por torneo
                $prefixes = $teamPrefixes[$tournamentIndex % count($teamPrefixes)];

                foreach ($prefixes as $prefix) {
                    $teamName = "{$prefix} {$game->short_name}";

                    $team = Team::create([
                        'tournament_id' => $tournament->id,
                        'name' => $teamName,
                        'description' => "Equipo {$teamName} del torneo {$tournamentName}",
                    ]);

                    // Crear jugadores para el equipo
                    for ($p = 0; $p < $game->team_size; $p++) {
                        $isCaptain = ($p === 0);
                        $userCounter++;

                        $user = User::create([
                            'name' => $isCaptain ? "Cap_{$prefix}" : "P{$userCounter}",
                            'email' => "user{$userCounter}@tournament.test",
                            'password' => Hash::make('password'),
                            'role' => 'user',
                            'email_verified_at' => now(),
                        ]);

                        $team->users()->attach($user->id, [
                            'role' => $isCaptain ? 'captain' : 'player',
                        ]);

                        $tournament->users()->attach($user->id, [
                            'status' => 'assigned',
                        ]);
                    }
                }

                $this->command->info("    → 8 equipos con {$game->team_size} jugadores cada uno");
                $tournamentIndex++;
            }
        }

        $this->command->info('');
        $this->command->info('✅ Torneos creados correctamente:');
        $this->command->info("   - {$tournamentIndex} torneos nuevos");
        $this->command->info("   - " . ($tournamentIndex * 8) . " equipos en total");
        $this->command->info("   - Listos para generar brackets desde la interfaz");
        $this->command->info('');
        $this->command->info('🎮 Accede como admin@admin.com y haz clic en "Generar Bracket"');
    }
}
