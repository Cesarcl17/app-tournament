<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'description',
        'icon',
        'user_id',
        'subject_type',
        'subject_id',
        'metadata',
        'is_public',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean',
    ];

    // Tipos de actividad
    public const TYPE_TOURNAMENT_CREATED = 'tournament_created';
    public const TYPE_TOURNAMENT_STARTED = 'tournament_started';
    public const TYPE_TOURNAMENT_FINISHED = 'tournament_finished';
    public const TYPE_BRACKET_GENERATED = 'bracket_generated';
    public const TYPE_TEAM_CREATED = 'team_created';
    public const TYPE_PLAYER_JOINED = 'player_joined';
    public const TYPE_MATCH_COMPLETED = 'match_completed';
    public const TYPE_CHAMPION_CROWNED = 'champion_crowned';

    // Iconos por tipo
    public static array $icons = [
        self::TYPE_TOURNAMENT_CREATED => '🏆',
        self::TYPE_TOURNAMENT_STARTED => '🚀',
        self::TYPE_TOURNAMENT_FINISHED => '🏁',
        self::TYPE_BRACKET_GENERATED => '📊',
        self::TYPE_TEAM_CREATED => '👥',
        self::TYPE_PLAYER_JOINED => '✨',
        self::TYPE_MATCH_COMPLETED => '⚔️',
        self::TYPE_CHAMPION_CROWNED => '👑',
    ];

    /**
     * Usuario que realizó la actividad
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Modelo relacionado (polimórfico)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Obtener icono de la actividad
     */
    public function getIconAttribute($value): string
    {
        return $value ?? self::$icons[$this->type] ?? '📌';
    }

    /**
     * Scope para actividades públicas
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope para actividades recientes
     */
    public function scopeRecent($query, int $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Crear actividad de torneo creado
     */
    public static function tournamentCreated(Tournament $tournament, ?User $user = null): self
    {
        return self::create([
            'type' => self::TYPE_TOURNAMENT_CREATED,
            'description' => "Se creó el torneo \"{$tournament->name}\"",
            'user_id' => $user?->id,
            'subject_type' => Tournament::class,
            'subject_id' => $tournament->id,
            'metadata' => [
                'tournament_name' => $tournament->name,
                'game' => $tournament->game?->name,
            ],
        ]);
    }

    /**
     * Crear actividad de bracket generado
     */
    public static function bracketGenerated(Tournament $tournament, ?User $user = null): self
    {
        return self::create([
            'type' => self::TYPE_BRACKET_GENERATED,
            'description' => "Se generó el bracket del torneo \"{$tournament->name}\"",
            'user_id' => $user?->id,
            'subject_type' => Tournament::class,
            'subject_id' => $tournament->id,
        ]);
    }

    /**
     * Crear actividad de equipo creado
     */
    public static function teamCreated(Team $team, ?User $user = null): self
    {
        return self::create([
            'type' => self::TYPE_TEAM_CREATED,
            'description' => "Se creó el equipo \"{$team->name}\" en \"{$team->tournament->name}\"",
            'user_id' => $user?->id,
            'subject_type' => Team::class,
            'subject_id' => $team->id,
            'metadata' => [
                'team_name' => $team->name,
                'tournament_name' => $team->tournament->name,
            ],
        ]);
    }

    /**
     * Crear actividad de jugador unido
     */
    public static function playerJoined(Team $team, User $player): self
    {
        return self::create([
            'type' => self::TYPE_PLAYER_JOINED,
            'description' => "{$player->name} se unió al equipo \"{$team->name}\"",
            'user_id' => $player->id,
            'subject_type' => Team::class,
            'subject_id' => $team->id,
            'metadata' => [
                'player_name' => $player->name,
                'team_name' => $team->name,
            ],
        ]);
    }

    /**
     * Crear actividad de partida completada
     */
    public static function matchCompleted(TournamentMatch $match): self
    {
        $winner = $match->winner;
        $loser = $match->winner_id === $match->team1_id ? $match->team2 : $match->team1;

        return self::create([
            'type' => self::TYPE_MATCH_COMPLETED,
            'description' => "{$winner->name} venció a {$loser->name} ({$match->score_team1}-{$match->score_team2})",
            'subject_type' => TournamentMatch::class,
            'subject_id' => $match->id,
            'metadata' => [
                'winner' => $winner->name,
                'loser' => $loser->name,
                'score' => "{$match->score_team1}-{$match->score_team2}",
                'tournament' => $match->tournament->name,
            ],
        ]);
    }

    /**
     * Crear actividad de campeón coronado
     */
    public static function championCrowned(Tournament $tournament, Team $champion): self
    {
        return self::create([
            'type' => self::TYPE_CHAMPION_CROWNED,
            'description' => "¡{$champion->name} es el campeón de \"{$tournament->name}\"!",
            'subject_type' => Tournament::class,
            'subject_id' => $tournament->id,
            'metadata' => [
                'champion' => $champion->name,
                'tournament' => $tournament->name,
            ],
        ]);
    }
}
