<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            [
                'name' => 'League of Legends',
                'slug' => 'league-of-legends',
                'short_name' => 'LoL',
                'description' => 'El juego MOBA más popular del mundo. Compite en equipos de 5 jugadores en intensas batallas estratégicas.',
                'logo' => 'images/games/lol.png',
                'team_size' => 5,
                'active' => true,
            ],
            [
                'name' => 'Overwatch 2',
                'slug' => 'overwatch',
                'short_name' => 'OW2',
                'description' => 'El shooter por equipos de Blizzard. Elige tu héroe y domina el campo de batalla en equipos de 5.',
                'logo' => 'images/games/overwatch.png',
                'team_size' => 5,
                'active' => true,
            ],
        ];

        foreach ($games as $gameData) {
            Game::updateOrCreate(
                ['slug' => $gameData['slug']],
                $gameData
            );
        }

        $this->command->info('Juegos creados: League of Legends, Overwatch 2');
    }
}
