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
        // Añadir logo a equipos
        Schema::table('teams', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('description');
        });

        // Añadir banner a torneos
        Schema::table('tournaments', function (Blueprint $table) {
            $table->string('banner')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('logo');
        });

        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('banner');
        });
    }
};
