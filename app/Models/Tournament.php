<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
