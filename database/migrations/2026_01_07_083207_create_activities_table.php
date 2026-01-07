<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // match_completed, team_created, tournament_created, player_joined, etc.
            $table->text('description');
            $table->string('icon')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->nullableMorphs('subject'); // Modelo relacionado (tournament, team, match, etc.)
            $table->json('metadata')->nullable(); // Datos adicionales
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
