<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'status',
        'message',
    ];

    /**
     * Equipo al que se solicita unirse
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Usuario que hace la solicitud
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si fue aprobada
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Verificar si fue rechazada
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
