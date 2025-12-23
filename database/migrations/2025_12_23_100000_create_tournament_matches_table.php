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
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('round'); // Número de ronda (1, 2, 3...)
            $table->unsignedSmallInteger('position'); // Posición dentro de la ronda
            $table->foreignId('team1_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('team2_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('winner_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->unsignedTinyInteger('score_team1')->nullable();
            $table->unsignedTinyInteger('score_team2')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->dateTime('scheduled_at')->nullable();
            $table->timestamps();

            // Índice compuesto para búsquedas eficientes
            $table->index(['tournament_id', 'round', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
