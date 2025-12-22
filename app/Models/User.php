<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Team;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    public function isPlayer(): bool
    {
        return $this->role === 'player';
    }

    public function isCaptain(): bool
    {
        return $this->role === 'captain';
    }

    /**
     * Verifica si el usuario puede gestionar torneos (admin u organizador)
     */
    public function canManageTournaments(): bool
    {
        return $this->isAdmin() || $this->isOrganizer();
    }

    /**
     * Verifica si el usuario puede gestionar un equipo específico
     */
    public function canManageTeam(Team $team): bool
    {
        if ($this->isAdmin() || $this->isOrganizer()) {
            return true;
        }

        // Capitán global que es capitán en ese equipo
        if ($this->isCaptain()) {
            return $team->users()
                ->where('user_id', $this->id)
                ->wherePivot('role', 'captain')
                ->exists();
        }

        return false;
    }

    /**
     * Roles disponibles para registro/perfil
     */
    public static function availableRoles(): array
    {
        return [
            'player' => 'Jugador',
            'captain' => 'Capitán',
        ];
    }

    /**
     * Torneos en los que el usuario está inscrito
     */
    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_user')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Verifica si el usuario está inscrito en un torneo
     */
    public function isRegisteredInTournament(Tournament $tournament): bool
    {
        return $this->tournaments()
            ->where('tournament_id', $tournament->id)
            ->exists();
    }

    /**
     * Verifica si el usuario ya tiene equipo en un torneo
     */
    public function hasTeamInTournament(Tournament $tournament): bool
    {
        return $this->teams()
            ->where('tournament_id', $tournament->id)
            ->exists();
    }
}
