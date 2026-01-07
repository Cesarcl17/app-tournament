<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class MatchComment extends Model
{
    protected $fillable = [
        'tournament_match_id',
        'user_id',
        'team_id',
        'content',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Partida a la que pertenece el comentario
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(TournamentMatch::class, 'tournament_match_id');
    }

    /**
     * Usuario que escribió el comentario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Equipo del usuario al momento de comentar
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Crear comentario de sistema
     */
    public static function createSystemMessage(TournamentMatch $match, string $content): self
    {
        return self::create([
            'tournament_match_id' => $match->id,
            'user_id' => Auth::id() ?? 1, // Admin por defecto
            'content' => $content,
            'is_system' => true,
        ]);
    }
}
