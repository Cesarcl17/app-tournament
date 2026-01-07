<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * Listado de juegos
     */
    public function index()
    {
        $games = Game::withCount('tournaments')
            ->orderBy('name')
            ->paginate(12);

        return view('admin.games.index', compact('games'));
    }

    /**
     * Formulario de crear juego
     */
    public function create()
    {
        return view('admin.games.create');
    }

    /**
     * Guardar nuevo juego
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:games',
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'team_sizes' => 'required|array|min:1',
            'team_sizes.*' => 'integer|min:1|max:20',
            'positions' => 'nullable|string|max:500',
            'active' => 'boolean',
        ]);

        // Generar slug
        $validated['slug'] = Str::slug($validated['name']);

        // Manejar subida de logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('games', 'public');
        }

        $validated['active'] = $request->has('active');
        
        // Ordenar team_sizes
        sort($validated['team_sizes']);

        // Procesar posiciones (texto separado por comas a array)
        if (!empty($validated['positions'])) {
            $positions = array_map('trim', explode(',', $validated['positions']));
            $validated['positions'] = array_filter($positions); // Eliminar vacíos
        } else {
            $validated['positions'] = null;
        }

        Game::create($validated);

        return redirect()->route('admin.games.index')
            ->with('success', 'Juego "' . $validated['name'] . '" creado exitosamente.');
    }

    /**
     * Formulario de editar juego
     */
    public function edit(Game $game)
    {
        return view('admin.games.edit', compact('game'));
    }

    /**
     * Actualizar juego
     */
    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:games,name,' . $game->id,
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'team_sizes' => 'required|array|min:1',
            'team_sizes.*' => 'integer|min:1|max:20',
            'positions' => 'nullable|string|max:500',
            'active' => 'boolean',
        ]);

        // Generar slug si cambió el nombre
        if ($game->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Manejar subida de logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($game->logo && Storage::disk('public')->exists($game->logo)) {
                Storage::disk('public')->delete($game->logo);
            }
            $validated['logo'] = $request->file('logo')->store('games', 'public');
        }

        $validated['active'] = $request->has('active');
        
        // Ordenar team_sizes
        sort($validated['team_sizes']);

        // Procesar posiciones (texto separado por comas a array)
        if (!empty($validated['positions'])) {
            $positions = array_map('trim', explode(',', $validated['positions']));
            $validated['positions'] = array_filter($positions); // Eliminar vacíos
        } else {
            $validated['positions'] = null;
        }

        $game->update($validated);

        return redirect()->route('admin.games.index')
            ->with('success', 'Juego "' . $game->name . '" actualizado exitosamente.');
    }

    /**
     * Eliminar juego
     */
    public function destroy(Game $game)
    {
        // Verificar que no tenga torneos
        if ($game->tournaments()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el juego porque tiene torneos asociados.');
        }

        // Eliminar logo si existe
        if ($game->logo && Storage::disk('public')->exists($game->logo)) {
            Storage::disk('public')->delete($game->logo);
        }

        $gameName = $game->name;
        $game->delete();

        return redirect()->route('admin.games.index')
            ->with('success', 'Juego "' . $gameName . '" eliminado exitosamente.');
    }

    /**
     * Eliminar logo del juego
     */
    public function deleteLogo(Game $game)
    {
        if ($game->logo && Storage::disk('public')->exists($game->logo)) {
            Storage::disk('public')->delete($game->logo);
        }

        $game->update(['logo' => null]);

        return back()->with('success', 'Logo eliminado exitosamente.');
    }
}
