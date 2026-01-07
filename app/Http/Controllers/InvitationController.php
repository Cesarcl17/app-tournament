<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    /**
     * Mostrar formulario para enviar invitación
     */
    public function create(Team $team)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$this->canInvite($user, $team)) {
            abort(403, 'No tienes permiso para invitar a este equipo.');
        }

        $pendingInvitations = $team->invitations()->pending()->get();

        return view('invitations.create', [
            'team' => $team,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }

    /**
     * Enviar invitación por email
     */
    public function store(Request $request, Team $team)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$this->canInvite($user, $team)) {
            abort(403, 'No tienes permiso para invitar a este equipo.');
        }

        $request->validate([
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:500',
        ]);

        $email = strtolower($request->email);

        // Verificar si ya existe invitación pendiente para este email
        $existingInvitation = TeamInvitation::where('team_id', $team->id)
            ->where('email', $email)
            ->pending()
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'Ya existe una invitación pendiente para este email.');
        }

        // Verificar si el email ya está en el equipo
        $existingMember = $team->users()->where('email', $email)->first();
        if ($existingMember) {
            return back()->with('error', 'Este usuario ya es miembro del equipo.');
        }

        // Crear invitación
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by' => $user->id,
            'email' => $email,
            'message' => $request->message,
        ]);

        // Enviar notificación por email
        try {
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $existingUser->notify(new TeamInvitationReceived($invitation));
            } else {
                // Para usuarios no registrados, usar notificación por mail directamente
                \Illuminate\Support\Facades\Mail::raw(
                    $this->getInvitationEmailContent($invitation),
                    function ($message) use ($email, $team) {
                        $message->to($email)
                                ->subject("Invitación a unirse al equipo {$team->name}");
                    }
                );
            }
        } catch (\Exception $e) {
            // Loggear error pero no fallar
            Log::error('Error enviando invitación: ' . $e->getMessage());
        }

        return back()->with('success', 'Invitación enviada a ' . $email);
    }

    /**
     * Ver invitación (página de aceptar/rechazar)
     */
    public function show(string $token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired()) {
            return view('invitations.expired', ['invitation' => $invitation]);
        }

        if ($invitation->status !== TeamInvitation::STATUS_PENDING) {
            return view('invitations.already-processed', ['invitation' => $invitation]);
        }

        return view('invitations.show', [
            'invitation' => $invitation,
            'isLoggedIn' => Auth::check(),
            'emailMatches' => Auth::check() && Auth::user()->email === $invitation->email,
        ]);
    }

    /**
     * Aceptar invitación
     */
    public function accept(Request $request, string $token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            // Redirigir a login con la invitación como parámetro
            session(['pending_invitation' => $token]);
            return redirect()->route('login')
                ->with('info', 'Inicia sesión o regístrate para aceptar la invitación.');
        }

        if (!$invitation->isPending()) {
            return redirect()->route('home')
                ->with('error', 'Esta invitación ya no es válida.');
        }

        // Verificar si el usuario ya tiene equipo en el torneo
        if ($user->hasTeamInTournament($invitation->team->tournament)) {
            return redirect()->route('torneos.show', $invitation->team->tournament)
                ->with('error', 'Ya tienes un equipo en este torneo.');
        }

        if ($invitation->accept($user)) {
            return redirect()->route('teams.show', $invitation->team)
                ->with('success', '¡Te has unido al equipo ' . $invitation->team->name . '!');
        }

        return redirect()->route('home')
            ->with('error', 'No se pudo procesar la invitación.');
    }

    /**
     * Rechazar invitación
     */
    public function reject(string $token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isPending()) {
            $invitation->reject();
        }

        return redirect()->route('home')
            ->with('info', 'Invitación rechazada.');
    }

    /**
     * Cancelar invitación (solo quien la envió)
     */
    public function destroy(TeamInvitation $invitation)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || ($invitation->invited_by !== $user->id && $user->role !== 'admin')) {
            abort(403);
        }

        if ($invitation->isPending()) {
            $invitation->delete();
            return back()->with('success', 'Invitación cancelada.');
        }

        return back()->with('error', 'Esta invitación ya fue procesada.');
    }

    /**
     * Verificar si el usuario puede invitar al equipo
     */
    private function canInvite(?User $user, Team $team): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->canManageTournaments()) {
            return true;
        }

        // Solo capitanes del equipo pueden invitar
        return $team->users()
            ->wherePivot('role', 'captain')
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * Generar contenido del email para usuarios no registrados
     */
    private function getInvitationEmailContent(TeamInvitation $invitation): string
    {
        $team = $invitation->team;
        $inviter = $invitation->inviter;
        $tournament = $team->tournament;
        $url = $invitation->getAcceptUrl();

        $content = "¡Hola!\n\n";
        $content .= "{$inviter->name} te ha invitado a unirte al equipo \"{$team->name}\" ";
        $content .= "en el torneo \"{$tournament->name}\".\n\n";

        if ($invitation->message) {
            $content .= "Mensaje: {$invitation->message}\n\n";
        }

        $content .= "Para aceptar la invitación, haz clic en el siguiente enlace:\n";
        $content .= "{$url}\n\n";
        $content .= "Si no tienes cuenta, podrás crear una al aceptar la invitación.\n\n";
        $content .= "Esta invitación expira el " . $invitation->expires_at->format('d/m/Y H:i') . ".\n\n";
        $content .= "¡Nos vemos en el torneo!";

        return $content;
    }
}
