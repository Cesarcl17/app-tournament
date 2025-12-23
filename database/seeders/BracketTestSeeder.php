<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BracketTestSeeder extends Seeder
{
    /**
     * Crear datos de prueba para testear el bracket:
     * - 80 usuarios (16 equipos x 5 jugadores)
     * - 16 equipos
     * - 16 capitanes (uno por equipo)
     */
    public function run(): void
    {
        $tournament = Tournament::first();

        if (!$tournament) {
            $this->command->error('No hay torneos creados. Crea primero un torneo.');
            return;
        }

        $this->command->info("Creando datos para torneo: {$tournament->name}");
        $this->command->info("Formato: {$tournament->getFormatLabel()}");

        $teamSize = $tournament->team_size;
        $numTeams = 16;

        $teamNames = [
            'Phoenix Rising', 'Dark Knights', 'Storm Breakers', 'Ice Dragons',
            'Thunder Wolves', 'Shadow Hunters', 'Fire Hawks', 'Steel Titans',
            'Cyber Warriors', 'Neon Vipers', 'Mystic Ravens', 'Atomic Bears',
            'Crystal Foxes', 'Golden Eagles', 'Plasma Panthers', 'Nova Stars'
        ];

        $userIndex = 1;

        foreach ($teamNames as $teamName) {
            $team = Team::create([
                'tournament_id' => $tournament->id,
                'name' => $teamName,
                'description' => "Equipo {$teamName} listo para competir",
            ]);

            for ($j = 0; $j < $teamSize; $j++) {
                $isCaptain = ($j === 0);

                $user = User::create([
                    'name' => $isCaptain ? "Captain {$teamName}" : "Player{$userIndex}",
                    'email' => $isCaptain 
                        ? strtolower(str_replace(' ', '', $teamName)) . ".captain@test.com"
                        : "player{$userIndex}@test.com",
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

                if (!$isCaptain) {
                    $userIndex++;
                }
            }

            $this->command->info("  ✓ Equipo: {$teamName}");
        }

        $this->command->info('');
        $this->command->info('✅ Creados ' . ($numTeams * $teamSize) . ' usuarios y ' . $numTeams . ' equipos');
        $this->command->info('📧 Contraseña para todos: password');
    }
}
