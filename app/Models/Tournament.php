<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'game_id',
        'team_size',
    ];

    protected $casts = [
        'team_size' => 'integer',
    ];

    /**
     * Formatos de equipo permitidos
     */
    public const TEAM_SIZES = [1, 3, 5];

    /**
     * Mínimo de equipos para generar bracket
     */
    public const MIN_TEAMS_FOR_BRACKET = 4;

    /**
     * Obtener etiqueta del formato (1v1, 3v3, 5v5)
     */
    public function getFormatLabel(): string
    {
        return $this->team_size . 'v' . $this->team_size;
    }

    /**
     * Juego al que pertenece el torneo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Partidas del bracket del torneo
     */
    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }

    /**
     * Jugadores inscritos en el torneo (sin equipo asignado)
     */
    public function registeredUsers()
    {
        return $this->belongsToMany(User::class, 'tournament_user')
            ->withPivot('status')
            ->withTimestamps()
            ->wherePivot('status', 'registered');
    }

    /**
     * Todos los jugadores inscritos (con y sin equipo)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'tournament_user')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Verificar si el torneo tiene bracket generado
     */
    public function hasBracket(): bool
    {
        return $this->matches()->exists();
    }

    /**
     * Verificar si se puede generar el bracket
     * (mínimo 4 equipos y sin bracket previo)
     */
    public function canGenerateBracket(): bool
    {
        return $this->teams()->count() >= self::MIN_TEAMS_FOR_BRACKET && !$this->hasBracket();
    }

    /**
     * Obtener el número total de rondas del bracket
     */
    public function getTotalRounds(): int
    {
        $teamsCount = $this->teams()->count();
        if ($teamsCount < 2) {
            return 0;
        }
        return (int) ceil(log($teamsCount, 2));
    }

    /**
     * Obtener las partidas agrupadas por ronda
     */
    public function getMatchesByRound(): array
    {
        $matches = $this->matches()
            ->with(['team1', 'team2', 'winner'])
            ->orderBy('round')
            ->orderBy('position')
            ->get();

        $byRound = [];
        foreach ($matches as $match) {
            $byRound[$match->round][] = $match;
        }

        return $byRound;
    }

    /**
     * Obtener el equipo campeón (ganador de la final)
     */
    public function getChampion(): ?Team
    {
        $totalRounds = $this->getTotalRounds();
        if ($totalRounds === 0) {
            return null;
        }

        $finalMatch = $this->matches()
            ->where('round', $totalRounds)
            ->where('position', 0)
            ->first();

        return $finalMatch?->winner;
    }

    /**
     * Eliminar el bracket completo
     */
    public function deleteBracket(): bool
    {
        return $this->matches()->delete() > 0;
    }

    /**
     * Verificar si el torneo ha finalizado (tiene campeón)
     */
    public function isFinished(): bool
    {
        return $this->getChampion() !== null;
    }
}
