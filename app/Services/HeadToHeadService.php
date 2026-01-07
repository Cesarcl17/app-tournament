<?php

namespace App\Services;

use App\Models\Team;
use App\Models\TournamentMatch;
use Illuminate\Support\Collection;

class HeadToHeadService
{
    /**
     * Obtener historial de enfrentamientos entre dos equipos
     */
    public function getHistory(Team $team1, Team $team2): array
    {
        $matches = TournamentMatch::where(function ($query) use ($team1, $team2) {
            $query->where('team1_id', $team1->id)->where('team2_id', $team2->id);
        })->orWhere(function ($query) use ($team1, $team2) {
            $query->where('team1_id', $team2->id)->where('team2_id', $team1->id);
        })
        ->whereNotNull('winner_id')
        ->with(['tournament', 'tournament.game'])
        ->orderBy('scheduled_at', 'desc')
        ->get();

        $team1Wins = $matches->where('winner_id', $team1->id)->count();
        $team2Wins = $matches->where('winner_id', $team2->id)->count();

        return [
            'team1' => $team1,
            'team2' => $team2,
            'matches' => $matches,
            'team1_wins' => $team1Wins,
            'team2_wins' => $team2Wins,
            'total_matches' => $matches->count(),
        ];
    }

    /**
     * Obtener todos los rivales de un equipo con estadísticas
     */
    public function getRivals(Team $team): Collection
    {
        // Obtener todas las partidas del equipo
        $matches = TournamentMatch::where(function ($query) use ($team) {
            $query->where('team1_id', $team->id)
                  ->orWhere('team2_id', $team->id);
        })
        ->whereNotNull('winner_id')
        ->with(['team1', 'team2'])
        ->get();

        // Agrupar por rival
        $rivals = [];
        foreach ($matches as $match) {
            $rivalId = $match->team1_id === $team->id ? $match->team2_id : $match->team1_id;
            $rival = $match->team1_id === $team->id ? $match->team2 : $match->team1;
            
            if (!$rival) continue;

            if (!isset($rivals[$rivalId])) {
                $rivals[$rivalId] = [
                    'team' => $rival,
                    'wins' => 0,
                    'losses' => 0,
                    'total' => 0,
                ];
            }

            $rivals[$rivalId]['total']++;
            if ($match->winner_id === $team->id) {
                $rivals[$rivalId]['wins']++;
            } else {
                $rivals[$rivalId]['losses']++;
            }
        }

        return collect($rivals)->sortByDesc('total');
    }

    /**
     * Obtener estadísticas generales del enfrentamiento
     */
    public function getDetailedStats(Team $team1, Team $team2): array
    {
        $history = $this->getHistory($team1, $team2);
        $matches = $history['matches'];

        if ($matches->isEmpty()) {
            return [
                'history' => $history,
                'avg_score_team1' => 0,
                'avg_score_team2' => 0,
                'last_winner' => null,
                'tournaments_faced' => 0,
            ];
        }

        // Calcular promedios de score
        $team1Scores = [];
        $team2Scores = [];

        foreach ($matches as $match) {
            if ($match->team1_id === $team1->id) {
                $team1Scores[] = $match->score_team1 ?? 0;
                $team2Scores[] = $match->score_team2 ?? 0;
            } else {
                $team1Scores[] = $match->score_team2 ?? 0;
                $team2Scores[] = $match->score_team1 ?? 0;
            }
        }

        // Torneos donde se enfrentaron
        $tournamentsCount = $matches->pluck('tournament_id')->unique()->count();

        return [
            'history' => $history,
            'avg_score_team1' => count($team1Scores) > 0 ? round(array_sum($team1Scores) / count($team1Scores), 1) : 0,
            'avg_score_team2' => count($team2Scores) > 0 ? round(array_sum($team2Scores) / count($team2Scores), 1) : 0,
            'last_winner' => $matches->first()?->winner,
            'tournaments_faced' => $tournamentsCount,
        ];
    }
}
