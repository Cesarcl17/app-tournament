<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        // Get user's teams for filtering
        $userTeams = $user ? $user->teams()->with('tournament')->get() : collect();

        // Get all tournaments for filtering
        $tournaments = Tournament::orderBy('name')->get();

        return view('calendar.index', compact('userTeams', 'tournaments'));
    }

    /**
     * Get matches for the calendar in JSON format.
     */
    public function matches(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        $filter = $request->get('filter', 'all'); // all, my_matches, my_team
        $tournamentId = $request->get('tournament_id');

        $query = TournamentMatch::with(['tournament.game', 'team1', 'team2', 'winner'])
            ->whereNotNull('scheduled_at');

        // Filter by tournament if specified
        if ($tournamentId) {
            $query->where('tournament_id', $tournamentId);
        }

        // Apply user-specific filters
        if ($user && $filter === 'my_matches') {
            // Get matches where user is in team1 or team2
            $userTeamIds = $user->teams()->pluck('teams.id')->toArray();
            $query->where(function ($q) use ($userTeamIds) {
                $q->whereIn('team1_id', $userTeamIds)
                    ->orWhereIn('team2_id', $userTeamIds);
            });
        } elseif ($user && $filter === 'my_team' && $request->has('team_id')) {
            $teamId = $request->get('team_id');
            $query->where(function ($q) use ($teamId) {
                $q->where('team1_id', $teamId)
                    ->orWhere('team2_id', $teamId);
            });
        }

        $matches = $query->get();

        $events = $matches->map(function (TournamentMatch $match) {
            $team1Name = $match->team1?->name ?? 'TBD';
            $team2Name = $match->team2?->name ?? 'TBD';

            // Determine color based on status
            $color = match ($match->status) {
                TournamentMatch::STATUS_COMPLETED => '#28a745', // green
                TournamentMatch::STATUS_IN_PROGRESS => '#ffc107', // yellow
                default => '#007bff', // blue
            };

            // If disputed, use red
            if ($match->result_status === TournamentMatch::RESULT_DISPUTED) {
                $color = '#dc3545';
            }

            return [
                'id' => $match->id,
                'title' => "{$team1Name} vs {$team2Name}",
                'start' => $match->scheduled_at->toIso8601String(),
                'end' => $match->scheduled_at->addHours(2)->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tournament_id' => $match->tournament_id,
                    'tournament_name' => $match->tournament->name,
                    'game_name' => $match->tournament->game?->name ?? 'N/A',
                    'game_logo' => $match->tournament->game?->logo,
                    'round_name' => $match->getRoundName(),
                    'status' => $match->status,
                    'result_status' => $match->result_status,
                    'team1_name' => $team1Name,
                    'team2_name' => $team2Name,
                    'winner_name' => $match->winner?->name,
                    'score' => $match->isCompleted()
                        ? "{$match->score_team1} - {$match->score_team2}"
                        : null,
                    'google_calendar_url' => $this->generateGoogleCalendarUrl($match),
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Generate Google Calendar URL for a match.
     */
    protected function generateGoogleCalendarUrl(TournamentMatch $match): string
    {
        $team1Name = $match->team1?->name ?? 'TBD';
        $team2Name = $match->team2?->name ?? 'TBD';

        $title = urlencode("{$team1Name} vs {$team2Name} - {$match->tournament->name}");
        $description = urlencode(
            "Torneo: {$match->tournament->name}\n" .
            "Ronda: {$match->getRoundName()}\n" .
            "Juego: " . ($match->tournament->game?->name ?? 'N/A')
        );

        $startDate = $match->scheduled_at->format('Ymd\THis\Z');
        $endDate = $match->scheduled_at->addHours(2)->format('Ymd\THis\Z');

        return "https://calendar.google.com/calendar/render?action=TEMPLATE" .
            "&text={$title}" .
            "&dates={$startDate}/{$endDate}" .
            "&details={$description}";
    }

    /**
     * Get Google Calendar URL for a specific match.
     */
    public function googleCalendarUrl(TournamentMatch $match): JsonResponse
    {
        return response()->json([
            'url' => $this->generateGoogleCalendarUrl($match),
        ]);
    }
}
