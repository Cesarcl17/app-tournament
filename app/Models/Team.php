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
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role')
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
}
