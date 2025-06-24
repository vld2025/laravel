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
        Schema::create('automazione_pdf', function (Blueprint $table) {
            $table->id();
            
            // Configurazione automazione
            $table->boolean('attiva')->default(false);
            $table->integer('giorno_invio')->default(1); // 1-31 del mese
            $table->time('ora_invio')->default('09:00'); // HH:MM
            
            // Email settings
            $table->json('email_destinatari'); // array di email
            $table->string('email_oggetto')->default('Spese Mensili - {mese} {anno}');
            $table->text('email_messaggio')->nullable();
            
            // Filtri utenti
            $table->json('utenti_inclusi')->nullable(); // array di user_id, null = tutti
            $table->boolean('solo_con_spese')->default(true); // invia solo se ci sono spese
            
            // Log ultima esecuzione
            $table->timestamp('ultima_esecuzione')->nullable();
            $table->json('ultimo_risultato')->nullable(); // log dell'ultima esecuzione
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automazione_pdf');
    }
};
