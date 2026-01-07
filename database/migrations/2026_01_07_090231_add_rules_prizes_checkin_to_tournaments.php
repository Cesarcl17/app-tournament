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
        Schema::table('tournaments', function (Blueprint $table) {
            $table->text('rules')->nullable()->after('description');
            $table->json('prizes')->nullable()->after('rules');
            $table->unsignedInteger('check_in_minutes')->default(15)->after('prizes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['rules', 'prizes', 'check_in_minutes']);
        });
    }
};
