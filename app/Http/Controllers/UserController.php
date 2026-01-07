<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display the public profile of a user.
     */
    public function show(User $user): View
    {
        // Load relationships
        $user->load(['statistics', 'trophies.game', 'trophies.tournament']);

        // Get user's teams with tournament info
        $teams = $user->teams()
            ->with(['tournament.game', 'statistics'])
            ->get();

        // Separate active and finished tournaments
        $now = now();
        
        $activeTeams = $teams->filter(function ($team) use ($now) {
            return $team->tournament->end_date === null || $team->tournament->end_date >= $now;
        });

        $finishedTeams = $teams->filter(function ($team) use ($now) {
            return $team->tournament->end_date !== null && $team->tournament->end_date < $now;
        })->sortByDesc(function ($team) {
            return $team->tournament->end_date;
        });

        // Get tournament results for finished tournaments
        $tournamentResults = $this->getTournamentResults($finishedTeams);

        return view('users.show', compact(
            'user',
            'activeTeams',
            'finishedTeams',
            'tournamentResults'
        ));
    }

    /**
     * Get the final position/result for each tournament.
     */
    protected function getTournamentResults($teams): array
    {
        $results = [];

        foreach ($teams as $team) {
            $tournament = $team->tournament;
            
            // Find the last match this team played
            $lastMatch = $tournament->matches()
                ->where(function ($q) use ($team) {
                    $q->where('team1_id', $team->id)
                      ->orWhere('team2_id', $team->id);
                })
                ->where('status', 'completed')
                ->orderBy('round', 'desc')
                ->first();

            if (!$lastMatch) {
                $results[$tournament->id] = [
                    'position' => 'Sin partidas',
                    'is_champion' => false,
                ];
                continue;
            }

            $totalRounds = $tournament->getTotalRounds();
            $isChampion = $lastMatch->round === $totalRounds && $lastMatch->winner_id === $team->id;

            if ($isChampion) {
                $position = '🏆 Campeón';
            } elseif ($lastMatch->round === $totalRounds) {
                $position = '🥈 Finalista';
            } elseif ($lastMatch->round === $totalRounds - 1) {
                $position = '🥉 Semifinalista';
            } else {
                $roundName = $lastMatch->getRoundName();
                $position = "Eliminado en {$roundName}";
            }

            $results[$tournament->id] = [
                'position' => $position,
                'is_champion' => $isChampion,
            ];
        }

        return $results;
    }
}
