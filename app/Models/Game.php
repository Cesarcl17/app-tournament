<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'description',
        'logo',
        'team_sizes',
        'positions',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'team_sizes' => 'array',
        'positions' => 'array',
    ];

    /**
     * Torneos de este juego
     */
    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    /**
     * Torneos activos de este juego
     */
    public function activeTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class)
            ->where('end_date', '>=', now())
            ->orWhereNull('end_date');
    }

    /**
     * Obtener URL del logo
     */
    public function getLogoUrl(): string
    {
        if ($this->logo && file_exists(public_path($this->logo))) {
            return asset($this->logo);
        }

        // Logo por defecto según el juego
        return asset('images/games/default.png');
    }

    /**
     * Scope para juegos activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Obtener por slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
