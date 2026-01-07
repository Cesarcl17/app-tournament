<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'name',
        'description',
        'logo',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role', 'primary_role', 'secondary_role')
            ->withTimestamps();
    }

    /**
     * Solicitudes de unión pendientes
     */
    public function pendingRequests()
    {
        return $this->hasMany(TeamRequest::class)->where('status', 'pending');
    }

    /**
     * Todas las solicitudes de unión
     */
    public function requests()
    {
        return $this->hasMany(TeamRequest::class);
    }

    /**
     * Obtener los capitanes del equipo
     */
    public function captains()
    {
        return $this->users()->wherePivot('role', 'captain');
    }

    /**
     * Estadísticas del equipo
     */
    public function statistics()
    {
        return $this->hasOne(TeamStatistic::class);
    }

    /**
     * Invitaciones del equipo
     */
    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * Obtener estadísticas o crear si no existen
     */
    public function getOrCreateStatistics(): TeamStatistic
    {
        return $this->statistics ?? TeamStatistic::create([
            'team_id' => $this->id,
            'wins' => 0,
            'losses' => 0,
            'matches_played' => 0,
            'tournaments_won' => 0,
            'current_win_streak' => 0,
            'best_win_streak' => 0,
        ]);
    }
}
