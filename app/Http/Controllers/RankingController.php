<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\TeamStatistic;
use App\Models\UserStatistic;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    /**
     * Mostrar rankings de jugadores y equipos
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'players'); // players o teams
        $sortBy = $request->get('sort', 'wins'); // wins, win_rate, streak, tournaments
        $gameFilter = $request->get('game');

        $games = Game::active()->orderBy('name')->get();

        if ($type === 'teams') {
            $rankings = $this->getTeamRankings($sortBy, $gameFilter);
        } else {
            $rankings = $this->getPlayerRankings($sortBy);
        }

        return view('rankings.index', compact('rankings', 'type', 'sortBy', 'games', 'gameFilter'));
    }

    /**
     * Obtener ranking de jugadores
     */
    private function getPlayerRankings(string $sortBy)
    {
        $query = UserStatistic::with('user')
            ->where('matches_played', '>', 0);

        switch ($sortBy) {
            case 'win_rate':
                // Ordenar por win rate, requiriendo mínimo 5 partidas
                $query->where('matches_played', '>=', 5)
                    ->orderByRaw('(wins / matches_played) DESC');
                break;
            case 'streak':
                $query->orderBy('best_win_streak', 'desc');
                break;
            case 'tournaments':
                $query->orderBy('tournaments_won', 'desc');
                break;
            case 'wins':
            default:
                $query->orderBy('wins', 'desc');
                break;
        }

        return $query->take(50)->get();
    }

    /**
     * Obtener ranking de equipos
     */
    private function getTeamRankings(string $sortBy, ?string $gameSlug = null)
    {
        $query = TeamStatistic::with(['team.tournament.game'])
            ->where('matches_played', '>', 0);

        // Filtrar por juego si se especifica
        if ($gameSlug) {
            $query->whereHas('team.tournament.game', function ($q) use ($gameSlug) {
                $q->where('slug', $gameSlug);
            });
        }

        switch ($sortBy) {
            case 'win_rate':
                $query->where('matches_played', '>=', 5)
                    ->orderByRaw('(wins / matches_played) DESC');
                break;
            case 'streak':
                $query->orderBy('best_win_streak', 'desc');
                break;
            case 'tournaments':
                $query->orderBy('tournaments_won', 'desc');
                break;
            case 'wins':
            default:
                $query->orderBy('wins', 'desc');
                break;
        }

        return $query->take(50)->get();
    }
}
