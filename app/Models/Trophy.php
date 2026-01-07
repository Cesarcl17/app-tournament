<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Trophy extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'tournament_id',
        'game_id',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Trophy $trophy) {
            if (empty($trophy->slug)) {
                $trophy->slug = Str::slug($trophy->name) . '-' . time();
            }
        });
    }

    /**
     * Get the tournament associated with this trophy.
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the game associated with this trophy.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the users who have earned this trophy.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trophy_user')
            ->withPivot(['tournament_id', 'team_id', 'earned_at'])
            ->withTimestamps();
    }

    /**
     * Create a trophy for a tournament winner.
     */
    public static function createForTournament(Tournament $tournament, Team $winningTeam): Trophy
    {
        $trophy = static::create([
            'name' => "Campeón de {$tournament->name}",
            'slug' => Str::slug("campeon-{$tournament->name}") . '-' . $tournament->id,
            'description' => "Trofeo otorgado al equipo campeón del torneo {$tournament->name}",
            'icon' => $tournament->game?->logo,
            'tournament_id' => $tournament->id,
            'game_id' => $tournament->game_id,
        ]);

        // Award trophy to all team members
        foreach ($winningTeam->users as $user) {
            $trophy->users()->attach($user->id, [
                'tournament_id' => $tournament->id,
                'team_id' => $winningTeam->id,
                'earned_at' => now(),
            ]);
        }

        return $trophy;
    }
}
