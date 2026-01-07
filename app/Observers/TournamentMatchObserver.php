<?php

namespace App\Observers;

use App\Models\TournamentMatch;
use App\Services\StatisticsService;

class TournamentMatchObserver
{
    public function __construct(
        protected StatisticsService $statisticsService
    ) {}

    /**
     * Handle the TournamentMatch "updated" event.
     */
    public function updated(TournamentMatch $match): void
    {
        // Check if match just became completed
        if ($match->wasChanged('status') && $match->status === TournamentMatch::STATUS_COMPLETED) {
            $this->statisticsService->recordMatchResult($match);
        }

        // Also trigger if winner was just set (for BYE matches or admin resolutions)
        if ($match->wasChanged('winner_id') && $match->winner_id && $match->status === TournamentMatch::STATUS_COMPLETED) {
            // Only record if not already recorded by status change
            if (!$match->wasChanged('status')) {
                $this->statisticsService->recordMatchResult($match);
            }
        }
    }
}
