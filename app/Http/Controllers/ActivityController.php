<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Mostrar el feed de actividades
     */
    public function index(Request $request)
    {
        $query = Activity::public()->with(['user', 'subject']);

        // Filtrar por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $activities = $query->orderBy('created_at', 'desc')
            ->paginate(30);

        $types = [
            Activity::TYPE_TOURNAMENT_CREATED => 'Torneos creados',
            Activity::TYPE_BRACKET_GENERATED => 'Brackets generados',
            Activity::TYPE_TEAM_CREATED => 'Equipos creados',
            Activity::TYPE_PLAYER_JOINED => 'Jugadores unidos',
            Activity::TYPE_MATCH_COMPLETED => 'Partidas completadas',
            Activity::TYPE_CHAMPION_CROWNED => 'Campeones',
        ];

        return view('activities.index', [
            'activities' => $activities,
            'types' => $types,
            'currentType' => $request->type,
        ]);
    }

    /**
     * API: Obtener actividades recientes (para widget/ajax)
     */
    public function recent(Request $request)
    {
        $limit = min($request->get('limit', 10), 50);

        $activities = Activity::public()
            ->with(['user'])
            ->recent($limit)
            ->get();

        return response()->json($activities);
    }
}
