<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Actualizar perfil del usuario
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:player,captain',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verificar contraseña actual si quiere cambiarla
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'La contraseña actual no es correcta',
                ]);
            }
            $user->password = $validated['password'];
        }

        // Verificar si puede cambiar a capitán (no puede si ya es capitán de un equipo)
        if ($validated['role'] !== $user->role) {
            // Si cambia de capitán a jugador, verificar que no sea capitán de ningún equipo
            if ($user->role === 'captain' && $validated['role'] === 'player') {
                $isTeamCaptain = $user->teams()
                    ->wherePivot('role', 'captain')
                    ->exists();

                if ($isTeamCaptain) {
                    return back()->withErrors([
                        'role' => 'No puedes cambiar a jugador mientras seas capitán de un equipo. Transfiere el liderazgo primero.',
                    ]);
                }
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil actualizado correctamente');
    }
}
