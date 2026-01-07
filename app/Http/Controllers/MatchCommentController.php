<?php

namespace App\Http\Controllers;

use App\Models\MatchComment;
use App\Models\TournamentMatch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchCommentController extends Controller
{
    /**
     * Mostrar comentarios de una partida
     */
    public function index(TournamentMatch $match)
    {
        $comments = $match->comments()
            ->with(['user', 'team'])
            ->orderBy('created_at', 'asc')
            ->get();

        /** @var User|null $user */
        $user = Auth::user();

        $canComment = $this->canUserComment($user, $match);

        return view('matches.comments', [
            'match' => $match,
            'comments' => $comments,
            'canComment' => $canComment,
            'userTeam' => $this->getUserTeamInMatch($user, $match),
        ]);
    }

    /**
     * Guardar nuevo comentario
     */
    public function store(Request $request, TournamentMatch $match)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$this->canUserComment($user, $match)) {
            abort(403, 'No tienes permiso para comentar en esta partida.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $userTeam = $this->getUserTeamInMatch($user, $match);

        MatchComment::create([
            'tournament_match_id' => $match->id,
            'user_id' => $user->id,
            'team_id' => $userTeam?->id,
            'content' => $request->content,
            'is_system' => false,
        ]);

        return redirect()->back()->with('success', 'Comentario añadido.');
    }

    /**
     * Eliminar comentario
     */
    public function destroy(MatchComment $comment)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        // Solo puede eliminar el autor o un admin
        if ($comment->user_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'No puedes eliminar este comentario.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comentario eliminado.');
    }

    /**
     * Verificar si el usuario puede comentar en la partida
     */
    private function canUserComment(?User $user, TournamentMatch $match): bool
    {
        if (!$user) {
            return false;
        }

        // Admins y organizadores siempre pueden comentar
        if ($user->canManageTournaments()) {
            return true;
        }

        // Solo capitanes de los equipos participantes pueden comentar
        return $match->isCaptainOfTeam1($user) || $match->isCaptainOfTeam2($user);
    }

    /**
     * Obtener el equipo del usuario en la partida
     */
    private function getUserTeamInMatch(?User $user, TournamentMatch $match)
    {
        if (!$user) {
            return null;
        }

        if ($match->isCaptainOfTeam1($user) || ($match->team1 && $match->team1->users->contains('id', $user->id))) {
            return $match->team1;
        }

        if ($match->isCaptainOfTeam2($user) || ($match->team2 && $match->team2->users->contains('id', $user->id))) {
            return $match->team2;
        }

        return null;
    }
}
