<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeamInvitation extends Model
{
    protected $fillable = [
        'team_id',
        'invited_by',
        'email',
        'token',
        'status',
        'message',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    // Estados
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(64);
            }
            if (empty($invitation->expires_at)) {
                $invitation->expires_at = Carbon::now()->addDays(7);
            }
        });
    }

    /**
     * Equipo al que se invita
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Usuario que envió la invitación
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Verificar si la invitación está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && !$this->isExpired();
    }

    /**
     * Verificar si la invitación ha expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Aceptar la invitación
     */
    public function accept(User $user): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        // Verificar que el usuario no esté ya en el equipo
        if ($this->team->users->contains('id', $user->id)) {
            return false;
        }

        // Verificar que el usuario no tenga otro equipo en el torneo
        if ($user->hasTeamInTournament($this->team->tournament)) {
            return false;
        }

        // Agregar al equipo
        $this->team->users()->attach($user->id, ['role' => 'player']);

        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        // Registrar actividad
        Activity::playerJoined($this->team, $user);

        return true;
    }

    /**
     * Rechazar la invitación
     */
    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
        ]);
    }

    /**
     * Scope para invitaciones pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope para invitaciones de un email
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', strtolower($email));
    }

    /**
     * Obtener URL de invitación
     */
    public function getAcceptUrl(): string
    {
        return route('invitations.accept', $this->token);
    }
}
