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
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->boolean('team1_checked_in')->default(false)->after('scheduled_at');
            $table->boolean('team2_checked_in')->default(false)->after('team1_checked_in');
            $table->timestamp('team1_checked_in_at')->nullable()->after('team2_checked_in');
            $table->timestamp('team2_checked_in_at')->nullable()->after('team1_checked_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropColumn(['team1_checked_in', 'team2_checked_in', 'team1_checked_in_at', 'team2_checked_in_at']);
        });
    }
};
