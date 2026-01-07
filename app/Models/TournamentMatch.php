<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'round',
        'position',
        'team1_id',
        'team2_id',
        'winner_id',
        'score_team1',
        'score_team2',
        'status',
        'scheduled_at',
        'team1_checked_in',
        'team2_checked_in',
        'team1_checked_in_at',
        'team2_checked_in_at',
        'score_team1_by_captain1',
        'score_team2_by_captain1',
        'reported_by_captain1',
        'reported_at_captain1',
        'score_team1_by_captain2',
        'score_team2_by_captain2',
        'reported_by_captain2',
        'reported_at_captain2',
        'result_status',
    ];

    protected $casts = [
        'round' => 'integer',
        'position' => 'integer',
        'score_team1' => 'integer',
        'score_team2' => 'integer',
        'score_team1_by_captain1' => 'integer',
        'score_team2_by_captain1' => 'integer',
        'score_team1_by_captain2' => 'integer',
        'score_team2_by_captain2' => 'integer',
        'scheduled_at' => 'datetime',
        'reported_at_captain1' => 'datetime',
        'reported_at_captain2' => 'datetime',
        'team1_checked_in' => 'boolean',
        'team2_checked_in' => 'boolean',
        'team1_checked_in_at' => 'datetime',
        'team2_checked_in_at' => 'datetime',
    ];

    // Estados de partida
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    // Estados de resultado por capitanes
    public const RESULT_PENDING = 'pending';
    public const RESULT_TEAM1_REPORTED = 'team1_reported';
    public const RESULT_TEAM2_REPORTED = 'team2_reported';
    public const RESULT_MATCHED = 'matched';
    public const RESULT_DISPUTED = 'disputed';
    public const RESULT_ADMIN_RESOLVED = 'admin_resolved';

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }

    /**
     * Comentarios de la partida
     */
    public function comments()
    {
        return $this->hasMany(MatchComment::class, 'tournament_match_id');
    }

    public function captainReporter1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_captain1');
    }

    public function captainReporter2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_captain2');
    }

    public function scopeByRound($query, int $round)
    {
        return $query->where('round', $round);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeDisputed($query)
    {
        return $query->where('result_status', self::RESULT_DISPUTED);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at');
    }

    public function isBye(): bool
    {
        return ($this->team1_id && !$this->team2_id) || (!$this->team1_id && $this->team2_id);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isDisputed(): bool
    {
        return $this->result_status === self::RESULT_DISPUTED;
    }

    public function canBeUpdated(): bool
    {
        return $this->team1_id && $this->team2_id && !$this->isCompleted();
    }

    public function hasTeams(): bool
    {
        return $this->team1_id && $this->team2_id;
    }

    /**
     * Verificar si un usuario es capitán del equipo 1
     */
    public function isCaptainOfTeam1(User $user): bool
    {
        if (!$this->team1) return false;
        return $this->team1->captains()->where('user_id', $user->id)->exists();
    }

    /**
     * Verificar si un usuario es capitán del equipo 2
     */
    public function isCaptainOfTeam2(User $user): bool
    {
        if (!$this->team2) return false;
        return $this->team2->captains()->where('user_id', $user->id)->exists();
    }

    /**
     * Verificar si un usuario puede reportar resultado en esta partida
     */
    public function canUserReport(User $user): bool
    {
        if ($this->isCompleted()) return false;
        if (!$this->hasTeams()) return false;

        return $this->isCaptainOfTeam1($user) || $this->isCaptainOfTeam2($user);
    }

    /**
     * Verificar si el capitán del equipo 1 ya reportó
     */
    public function hasTeam1Reported(): bool
    {
        return $this->reported_by_captain1 !== null;
    }

    /**
     * Verificar si el capitán del equipo 2 ya reportó
     */
    public function hasTeam2Reported(): bool
    {
        return $this->reported_by_captain2 !== null;
    }

    /**
     * Reportar resultado por un capitán
     */
    public function reportResult(User $user, int $scoreTeam1, int $scoreTeam2): array
    {
        if (!$this->canUserReport($user)) {
            return ['success' => false, 'message' => 'No tienes permiso para reportar este resultado'];
        }

        $isTeam1Captain = $this->isCaptainOfTeam1($user);
        $isTeam2Captain = $this->isCaptainOfTeam2($user);

        if ($isTeam1Captain) {
            if ($this->hasTeam1Reported()) {
                return ['success' => false, 'message' => 'Ya has reportado el resultado'];
            }
            $this->score_team1_by_captain1 = $scoreTeam1;
            $this->score_team2_by_captain1 = $scoreTeam2;
            $this->reported_by_captain1 = $user->id;
            $this->reported_at_captain1 = now();
            $this->result_status = self::RESULT_TEAM1_REPORTED;
        } elseif ($isTeam2Captain) {
            if ($this->hasTeam2Reported()) {
                return ['success' => false, 'message' => 'Ya has reportado el resultado'];
            }
            $this->score_team1_by_captain2 = $scoreTeam1;
            $this->score_team2_by_captain2 = $scoreTeam2;
            $this->reported_by_captain2 = $user->id;
            $this->reported_at_captain2 = now();
            $this->result_status = self::RESULT_TEAM2_REPORTED;
        }

        $this->save();

        // Verificar si ambos han reportado
        if ($this->hasTeam1Reported() && $this->hasTeam2Reported()) {
            return $this->validateResults();
        }

        return ['success' => true, 'message' => 'Resultado reportado. Esperando al otro capitán.'];
    }

    /**
     * Validar si los resultados de ambos capitanes coinciden
     */
    public function validateResults(): array
    {
        $match1 = $this->score_team1_by_captain1 === $this->score_team1_by_captain2
                  && $this->score_team2_by_captain1 === $this->score_team2_by_captain2;

        if ($match1) {
            $this->result_status = self::RESULT_MATCHED;

            $scoreTeam1 = $this->score_team1_by_captain1;
            $scoreTeam2 = $this->score_team2_by_captain1;

            $winnerId = $scoreTeam1 > $scoreTeam2 ? $this->team1_id : $this->team2_id;

            $this->setWinner($winnerId, $scoreTeam1, $scoreTeam2);

            return ['success' => true, 'message' => '¡Resultados coinciden! Partida validada automáticamente.'];
        } else {
            $this->result_status = self::RESULT_DISPUTED;
            $this->save();

            return [
                'success' => false,
                'message' => 'Los resultados no coinciden. Un administrador revisará la disputa.',
                'disputed' => true
            ];
        }
    }

    /**
     * Resolver disputa por admin
     */
    public function resolveDispute(int $winnerId, int $scoreTeam1, int $scoreTeam2): bool
    {
        if ($winnerId !== $this->team1_id && $winnerId !== $this->team2_id) {
            return false;
        }

        $this->result_status = self::RESULT_ADMIN_RESOLVED;
        $this->setWinner($winnerId, $scoreTeam1, $scoreTeam2);

        return true;
    }

    public function getNextMatch(): ?TournamentMatch
    {
        $totalRounds = $this->tournament->getTotalRounds();

        if ($this->round >= $totalRounds) {
            return null;
        }

        $nextRound = $this->round + 1;
        $nextPosition = (int) floor($this->position / 2);

        return TournamentMatch::where('tournament_id', $this->tournament_id)
            ->where('round', $nextRound)
            ->where('position', $nextPosition)
            ->first();
    }

    public function setWinner(int $winnerId, ?int $scoreTeam1 = null, ?int $scoreTeam2 = null): bool
    {
        if ($winnerId !== $this->team1_id && $winnerId !== $this->team2_id) {
            return false;
        }

        $this->winner_id = $winnerId;
        $this->score_team1 = $scoreTeam1;
        $this->score_team2 = $scoreTeam2;
        $this->status = self::STATUS_COMPLETED;
        $this->save();

        $nextMatch = $this->getNextMatch();
        if ($nextMatch) {
            if ($this->position % 2 === 0) {
                $nextMatch->team1_id = $winnerId;
            } else {
                $nextMatch->team2_id = $winnerId;
            }
            $nextMatch->save();
        }

        return true;
    }

    public function getRoundName(): string
    {
        $totalRounds = $this->tournament->getTotalRounds();
        $roundsFromEnd = $totalRounds - $this->round + 1;

        return match ($roundsFromEnd) {
            1 => 'Final',
            2 => 'Semifinales',
            3 => 'Cuartos de Final',
            4 => 'Octavos de Final',
            5 => 'Dieciseisavos',
            default => "Ronda {$this->round}",
        };
    }

    public function getByeTeam(): ?Team
    {
        if (!$this->isBye()) {
            return null;
        }
        return $this->team1_id ? $this->team1 : $this->team2;
    }

    /**
     * Obtener etiqueta del estado del resultado
     */
    public function getResultStatusLabel(): string
    {
        return match ($this->result_status) {
            self::RESULT_PENDING => 'Pendiente',
            self::RESULT_TEAM1_REPORTED => 'Esperando equipo 2',
            self::RESULT_TEAM2_REPORTED => 'Esperando equipo 1',
            self::RESULT_MATCHED => 'Validado',
            self::RESULT_DISPUTED => '⚠️ En disputa',
            self::RESULT_ADMIN_RESOLVED => 'Resuelto por admin',
            default => 'Desconocido',
        };
    }

    /**
     * Verificar si el check-in está disponible para esta partida
     */
    public function isCheckInOpen(): bool
    {
        if (!$this->scheduled_at || !$this->hasTeams() || $this->isBye() || $this->isCompleted()) {
            return false;
        }

        $checkInMinutes = $this->tournament->check_in_minutes ?? 15;
        $deadline = $this->scheduled_at;
        $openTime = $this->scheduled_at->copy()->subMinutes($checkInMinutes * 2);

        return Carbon::now()->between($openTime, $deadline);
    }

    /**
     * Verificar si el check-in ha expirado
     */
    public function isCheckInExpired(): bool
    {
        if (!$this->scheduled_at) {
            return false;
        }

        return Carbon::now()->isAfter($this->scheduled_at);
    }

    /**
     * Realizar check-in de un equipo
     */
    public function checkIn(Team $team): bool
    {
        if (!$this->isCheckInOpen()) {
            return false;
        }

        if ($team->id === $this->team1_id) {
            $this->team1_checked_in = true;
            $this->team1_checked_in_at = now();
            $this->save();
            return true;
        }

        if ($team->id === $this->team2_id) {
            $this->team2_checked_in = true;
            $this->team2_checked_in_at = now();
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Verificar si ambos equipos han hecho check-in
     */
    public function bothTeamsCheckedIn(): bool
    {
        return $this->team1_checked_in && $this->team2_checked_in;
    }

    /**
     * Obtener estado del check-in para mostrar
     */
    public function getCheckInStatus(): array
    {
        $checkInMinutes = $this->tournament->check_in_minutes ?? 15;

        return [
            'team1' => $this->team1_checked_in,
            'team2' => $this->team2_checked_in,
            'is_open' => $this->isCheckInOpen(),
            'is_expired' => $this->isCheckInExpired(),
            'deadline' => $this->scheduled_at,
            'opens_at' => $this->scheduled_at ? $this->scheduled_at->copy()->subMinutes($checkInMinutes * 2) : null,
        ];
    }
}
