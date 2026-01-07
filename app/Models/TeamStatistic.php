<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamStatistic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'team_id',
        'wins',
        'losses',
        'matches_played',
        'tournaments_won',
        'current_win_streak',
        'best_win_streak',
    ];

    /**
     * Get the team that owns the statistics.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get win rate percentage.
     */
    public function getWinRateAttribute(): float
    {
        if ($this->matches_played === 0) {
            return 0;
        }

        return round(($this->wins / $this->matches_played) * 100, 1);
    }

    /**
     * Increment a win for this team.
     */
    public function recordWin(): void
    {
        $this->wins++;
        $this->matches_played++;
        $this->current_win_streak++;

        if ($this->current_win_streak > $this->best_win_streak) {
            $this->best_win_streak = $this->current_win_streak;
        }

        $this->save();
    }

    /**
     * Increment a loss for this team.
     */
    public function recordLoss(): void
    {
        $this->losses++;
        $this->matches_played++;
        $this->current_win_streak = 0;

        $this->save();
    }

    /**
     * Record tournament win.
     */
    public function recordTournamentWin(): void
    {
        $this->tournaments_won++;
        $this->save();
    }
}
