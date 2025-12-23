<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Añade campos para que cada capitán reporte su resultado.
     * Si ambos coinciden → se valida automático
     * Si no coinciden → disputa para admin
     */
    public function up(): void
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            // Resultado reportado por capitán del equipo 1
            $table->unsignedTinyInteger('score_team1_by_captain1')->nullable()->after('score_team2');
            $table->unsignedTinyInteger('score_team2_by_captain1')->nullable()->after('score_team1_by_captain1');
            $table->foreignId('reported_by_captain1')->nullable()->after('score_team2_by_captain1')
                ->constrained('users')->onDelete('set null');
            $table->timestamp('reported_at_captain1')->nullable()->after('reported_by_captain1');

            // Resultado reportado por capitán del equipo 2
            $table->unsignedTinyInteger('score_team1_by_captain2')->nullable()->after('reported_at_captain1');
            $table->unsignedTinyInteger('score_team2_by_captain2')->nullable()->after('score_team1_by_captain2');
            $table->foreignId('reported_by_captain2')->nullable()->after('score_team2_by_captain2')
                ->constrained('users')->onDelete('set null');
            $table->timestamp('reported_at_captain2')->nullable()->after('reported_by_captain2');

            // Estado del resultado: pending, team1_reported, team2_reported, matched, disputed, admin_resolved
            $table->string('result_status', 20)->default('pending')->after('reported_at_captain2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropForeign(['reported_by_captain1']);
            $table->dropForeign(['reported_by_captain2']);
            $table->dropColumn([
                'score_team1_by_captain1',
                'score_team2_by_captain1',
                'reported_by_captain1',
                'reported_at_captain1',
                'score_team1_by_captain2',
                'score_team2_by_captain2',
                'reported_by_captain2',
                'reported_at_captain2',
                'result_status',
            ]);
        });
    }
};
