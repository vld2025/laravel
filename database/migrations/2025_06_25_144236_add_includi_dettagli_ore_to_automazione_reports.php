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
        Schema::table('automazione_reports', function (Blueprint $table) {
            $table->boolean('includi_dettagli_ore')->default(true)->after('raggruppa_per_giorno')
                ->comment('Includi dettagli ore e chilometri nelle email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automazione_reports', function (Blueprint $table) {
            $table->dropColumn('includi_dettagli_ore');
        });
    }
};
