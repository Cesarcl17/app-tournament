<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Añadir columna positions a games
        Schema::table('games', function (Blueprint $table) {
            $table->json('positions')->nullable()->after('team_sizes');
        });

        // Añadir columnas de rol a team_user
        Schema::table('team_user', function (Blueprint $table) {
            $table->string('primary_role')->nullable()->after('role');
            $table->string('secondary_role')->nullable()->after('primary_role');
        });

        // Poblar posiciones para juegos existentes
        $gamePositions = [
            'league-of-legends' => ['Top', 'Jungle', 'Mid', 'ADC', 'Support'],
            'lol' => ['Top', 'Jungle', 'Mid', 'ADC', 'Support'],
            'overwatch' => ['Tank', 'DPS', 'Support'],
            'overwatch-2' => ['Tank', 'DPS', 'Support'],
            'counter-strike' => ['Entry', 'AWPer', 'Support', 'Lurker', 'IGL'],
            'cs2' => ['Entry', 'AWPer', 'Support', 'Lurker', 'IGL'],
            'valorant' => ['Duelist', 'Controller', 'Initiator', 'Sentinel'],
        ];

        foreach ($gamePositions as $slug => $positions) {
            DB::table('games')
                ->where('slug', $slug)
                ->update(['positions' => json_encode($positions)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropColumn(['primary_role', 'secondary_role']);
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('positions');
        });
    }
};
