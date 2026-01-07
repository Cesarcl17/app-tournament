<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'users' => User::count(),
            'tournaments' => Tournament::count(),
            'teams' => Team::count(),
            'matches' => TournamentMatch::count(),
            'completedMatches' => TournamentMatch::where('status', 'completed')->count(),
            'activeTournaments' => Tournament::whereDate('end_date', '>=', now())->count(),
        ];

        // Usuarios registrados por mes (últimos 6 meses)
        $usersByMonth = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromDate($item->year, $item->month, 1);
                return [
                    'label' => $date->format('M Y'),
                    'count' => $item->count,
                ];
            });

        // Torneos por juego
        $tournamentsByGame = Tournament::select('game_id', DB::raw('COUNT(*) as count'))
            ->groupBy('game_id')
            ->with('game')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->game?->name ?? 'Sin juego',
                    'count' => $item->count,
                ];
            });

        // Partidas por estado
        $matchesByStatus = [
            ['label' => 'Pendientes', 'count' => TournamentMatch::where('status', 'pending')->count()],
            ['label' => 'En progreso', 'count' => TournamentMatch::where('status', 'in_progress')->count()],
            ['label' => 'Completadas', 'count' => TournamentMatch::where('status', 'completed')->count()],
        ];

        // Disputas activas
        $activeDisputes = TournamentMatch::where('result_status', 'disputed')
            ->with(['tournament', 'team1', 'team2'])
            ->latest()
            ->take(5)
            ->get();

        // Últimos torneos
        $recentTournaments = Tournament::with('game')
            ->latest()
            ->take(5)
            ->get();

        // Últimos usuarios
        $recentUsers = User::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'usersByMonth',
            'tournamentsByGame',
            'matchesByStatus',
            'activeDisputes',
            'recentTournaments',
            'recentUsers'
        ));
    }
}
