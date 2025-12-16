<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::all();
        return view('torneos.index', compact('tournaments'));
    }

    public function create()
    {
        return view('torneos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Tournament::create($request->all());

        return redirect()
            ->route('torneos.index')
            ->with('success', 'Torneo creado correctamente');
    }

    public function show(Tournament $tournament)
    {
        $teams = $tournament->teams;
        return view('torneos.show', compact('tournament', 'teams'));
    }

    public function edit(Tournament $tournament)
    {
        return view('torneos.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tournament->update($request->all());

        return redirect()
            ->route('torneos.show', $tournament)
            ->with('success', 'Torneo actualizado correctamente');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();

        return redirect()
            ->route('torneos.index')
            ->with('success', 'Torneo eliminado correctamente');
    }
}
