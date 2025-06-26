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
            // Rimuove colonna includi_festivi
            $table->dropColumn('includi_festivi');
            
            // Aggiunge colonna per raggruppamento
            $table->boolean('raggruppa_per_giorno')->default(true)->after('solo_giorni_lavorativi')
                ->comment('Raggruppa tutti i report del giorno in una singola email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automazione_reports', function (Blueprint $table) {
            $table->dropColumn('raggruppa_per_giorno');
            $table->boolean('includi_festivi')->default(false);
        });
    }
};
