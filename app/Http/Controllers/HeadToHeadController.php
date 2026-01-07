<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\HeadToHeadService;
use Illuminate\Http\Request;

class HeadToHeadController extends Controller
{
    public function __construct(
        protected HeadToHeadService $headToHeadService
    ) {}

    /**
     * Mostrar historial de enfrentamientos entre dos equipos
     */
    public function show(Team $team1, Team $team2)
    {
        $stats = $this->headToHeadService->getDetailedStats($team1, $team2);

        return view('head-to-head.show', [
            'team1' => $team1,
            'team2' => $team2,
            'history' => $stats['history'],
            'stats' => $stats,
        ]);
    }

    /**
     * Ver todos los rivales de un equipo
     */
    public function rivals(Team $team)
    {
        $rivals = $this->headToHeadService->getRivals($team);

        return view('head-to-head.rivals', [
            'team' => $team,
            'rivals' => $rivals,
        ]);
    }
}
