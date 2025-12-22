<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener juegos activos con conteo de torneos
        $games = Game::active()
            ->withCount('tournaments')
            ->orderBy('name')
            ->get();

        // Obtener próximos torneos (los más cercanos a comenzar)
        $upcomingTournaments = Tournament::with('game')
            ->where(function ($query) {
                $query->where('start_date', '>=', now())
                    ->orWhereNull('start_date');
            })
            ->orderBy('start_date')
            ->limit(4)
            ->get();

        return view('inicio', compact('games', 'upcomingTournaments'));
    }
}
