<?php

namespace App\Services;

use App\Models\Team;
use App\Models\TeamStatistic;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\Trophy;
use App\Models\User;
use App\Models\UserStatistic;

class StatisticsService
{
    /**
     * Update statistics when a match is completed.
     */
    public function recordMatchResult(TournamentMatch $match): void
    {
        if (!$match->winner_id || !$match->team1_id || !$match->team2_id) {
            return;
        }

        $winningTeam = $match->winner;
        $losingTeam = $match->winner_id === $match->team1_id ? $match->team2 : $match->team1;

        // Update team statistics
        $this->recordTeamWin($winningTeam);
        $this->recordTeamLoss($losingTeam);

        // Update user statistics for winning team members
        foreach ($winningTeam->users as $user) {
            $this->recordUserWin($user);
        }

        // Update user statistics for losing team members
        foreach ($losingTeam->users as $user) {
            $this->recordUserLoss($user);
        }

        // Check if this is the final match and award trophy
        $this->checkAndAwardTrophy($match);
    }

    /**
     * Record a win for a team.
     */
    public function recordTeamWin(Team $team): void
    {
        $stats = $this->getOrCreateTeamStatistics($team);
        $stats->recordWin();
    }

    /**
     * Record a loss for a team.
     */
    public function recordTeamLoss(Team $team): void
    {
        $stats = $this->getOrCreateTeamStatistics($team);
        $stats->recordLoss();
    }

    /**
     * Record a win for a user.
     */
    public function recordUserWin(User $user): void
    {
        $stats = $this->getOrCreateUserStatistics($user);
        $stats->recordWin();
    }

    /**
     * Record a loss for a user.
     */
    public function recordUserLoss(User $user): void
    {
        $stats = $this->getOrCreateUserStatistics($user);
        $stats->recordLoss();
    }

    /**
     * Get or create user statistics.
     */
    public function getOrCreateUserStatistics(User $user): UserStatistic
    {
        return UserStatistic::firstOrCreate(
            ['user_id' => $user->id],
            [
                'wins' => 0,
                'losses' => 0,
                'matches_played' => 0,
                'tournaments_played' => 0,
                'tournaments_won' => 0,
                'current_win_streak' => 0,
                'best_win_streak' => 0,
            ]
        );
    }

    /**
     * Get or create team statistics.
     */
    public function getOrCreateTeamStatistics(Team $team): TeamStatistic
    {
        return TeamStatistic::firstOrCreate(
            ['team_id' => $team->id],
            [
                'wins' => 0,
                'losses' => 0,
                'matches_played' => 0,
                'tournaments_won' => 0,
                'current_win_streak' => 0,
                'best_win_streak' => 0,
            ]
        );
    }

    /**
     * Check if match is the final and award trophy to winner.
     */
    public function checkAndAwardTrophy(TournamentMatch $match): void
    {
        $tournament = $match->tournament;
        $totalRounds = $tournament->getTotalRounds();

        // Check if this is the final match (last round)
        if ($match->round !== $totalRounds) {
            return;
        }

        $winningTeam = $match->winner;

        if (!$winningTeam) {
            return;
        }

        // Check if trophy already exists for this tournament
        $existingTrophy = Trophy::where('tournament_id', $tournament->id)->first();
        if ($existingTrophy) {
            return;
        }

        // Create trophy and award to team members
        $trophy = Trophy::createForTournament($tournament, $winningTeam);

        // Update statistics for tournament win
        $teamStats = $this->getOrCreateTeamStatistics($winningTeam);
        $teamStats->recordTournamentWin();

        foreach ($winningTeam->users as $user) {
            $userStats = $this->getOrCreateUserStatistics($user);
            $userStats->recordTournamentWin();
        }
    }

    /**
     * Record tournament participation for all users in a tournament.
     */
    public function recordTournamentParticipation(Tournament $tournament): void
    {
        foreach ($tournament->teams as $team) {
            foreach ($team->users as $user) {
                $stats = $this->getOrCreateUserStatistics($user);
                $stats->recordTournamentParticipation();
            }
        }
    }

    /**
     * Recalculate all statistics from existing matches.
     * Useful for historical data.
     */
    public function recalculateAllStatistics(): array
    {
        // Reset all statistics
        UserStatistic::query()->delete();
        TeamStatistic::query()->delete();

        $matchesProcessed = 0;
        $trophiesAwarded = 0;

        // Process all completed matches
        $matches = TournamentMatch::where('status', TournamentMatch::STATUS_COMPLETED)
            ->whereNotNull('winner_id')
            ->with(['team1.users', 'team2.users', 'winner', 'tournament.game'])
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($matches as $match) {
            $this->recordMatchResult($match);
            $matchesProcessed++;
        }

        $trophiesAwarded = Trophy::count();

        return [
            'matches_processed' => $matchesProcessed,
            'trophies_awarded' => $trophiesAwarded,
            'users_with_stats' => UserStatistic::count(),
            'teams_with_stats' => TeamStatistic::count(),
        ];
    }
}
