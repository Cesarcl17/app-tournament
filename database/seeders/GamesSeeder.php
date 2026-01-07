<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GamesSeeder extends Seeder
{
    public function run()
    {
        // Elimina todos los juegos existentes antes de insertar los nuevos (sin romper claves foráneas)
        \App\Models\Game::query()->delete();

        $games = [
            [
                'name' => 'League of Legends',
                'slug' => 'league-of-legends',
                'short_name' => 'LoL',
                'genre' => 'MOBA',
                'platform' => 'PC',
                'developer' => 'Riot Games',
                'release_year' => 2009,
                'cover_url' => 'https://upload.wikimedia.org/wikipedia/commons/7/77/League_of_Legends_logo.svg',
                'description' => 'El juego MOBA más popular del mundo. Compite en equipos de 5 jugadores en intensas batallas estratégicas.',
                'team_size' => 5,
            ],
            [
                'name' => 'Overwatch 2',
                'slug' => 'overwatch-2',
                'short_name' => 'OW2',
                'genre' => 'FPS',
                'platform' => 'PC, PlayStation, Xbox, Switch',
                'developer' => 'Blizzard Entertainment',
                'release_year' => 2022,
                'cover_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/5e/Overwatch_2_logo.svg',
                'description' => 'El shooter por equipos de Blizzard. Elige tu héroe y domina el campo de batalla en partidas 5v5.',
                'team_size' => 5,
            ],
            [
                'name' => 'EA Sports FC 26',
                'slug' => 'ea-sports-fc-26',
                'short_name' => 'FC26',
                'genre' => 'Sports',
                'platform' => 'PlayStation 5',
                'developer' => 'EA Sports',
                'release_year' => 2025,
                'cover_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/EA_Sports_FC_24_logo.svg',
                'description' => 'El simulador de fútbol más realista. Compite en torneos y demuestra tu habilidad en la cancha.',
                'team_size' => 11,
            ],
        ];
        // ...existing code...

        foreach ($games as $gameData) {
            Game::updateOrCreate(
                ['name' => $gameData['name']],
                $gameData
            );
        }

        $this->command->info('✅ ' . count($games) . ' juegos creados/actualizados');
    }
}
