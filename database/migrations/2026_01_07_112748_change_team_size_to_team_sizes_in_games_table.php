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
        // Primero añadimos la nueva columna
        Schema::table('games', function (Blueprint $table) {
            $table->json('team_sizes')->nullable()->after('team_size');
        });

        // Migrar datos existentes: convertir team_size a array en team_sizes
        $games = DB::table('games')->get();
        foreach ($games as $game) {
            DB::table('games')
                ->where('id', $game->id)
                ->update(['team_sizes' => json_encode([$game->team_size])]);
        }

        // Eliminar columna antigua
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('team_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Añadir columna team_size de vuelta
        Schema::table('games', function (Blueprint $table) {
            $table->integer('team_size')->default(5)->after('description');
        });

        // Migrar datos: tomar el primer valor del array
        $games = DB::table('games')->get();
        foreach ($games as $game) {
            $sizes = json_decode($game->team_sizes, true);
            $firstSize = $sizes[0] ?? 5;
            DB::table('games')
                ->where('id', $game->id)
                ->update(['team_size' => $firstSize]);
        }

        // Eliminar columna team_sizes
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('team_sizes');
        });
    }
};
