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
        Schema::create('automazione_reports', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->comment('Nome configurazione');
            $table->boolean('attivo')->default(false);
            $table->time('ora_invio')->comment('Ora giornaliera di invio (es: 18:00)');
            $table->json('email_destinatari')->comment('Array di email destinatari');
            $table->json('lingue')->comment('Array di lingue da includere (it,en,de,ru)');
            $table->boolean('solo_giorni_lavorativi')->default(true)->comment('Invia solo nei giorni lavorativi');
            $table->boolean('includi_festivi')->default(false)->comment('Includi report dei giorni festivi');
            $table->string('formato_file')->default('pdf')->comment('Formato file: pdf, excel');
            $table->text('note')->nullable();
            $table->timestamp('ultimo_invio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automazione_reports');
    }
};
