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
        Schema::create('impostazioni_fattura', function (Blueprint $table) {
            $table->id();
            
            // Relazione unica con committente
            $table->foreignId('committente_id')->unique()->constrained('committenti')->onDelete('cascade');
            
            // Dati fatturazione obbligatori
            $table->text('indirizzo_fatturazione');
            $table->string('partita_iva');
            $table->string('iban');
            
            // Configurazioni fatturazione
            $table->boolean('swiss_qr_bill')->default(false);
            $table->boolean('fatturazione_automatica')->default(false);
            $table->integer('giorno_fatturazione')->nullable(); // 1-31
            $table->json('email_destinatari')->nullable(); // array di email
            
            // Costi base
            $table->decimal('costo_orario', 10, 2);
            $table->decimal('costo_km', 10, 2);
            $table->decimal('costo_pranzo', 10, 2)->nullable();
            $table->decimal('costo_trasferta', 10, 2)->nullable();
            $table->decimal('costo_fisso_intervento', 10, 2)->nullable();
            
            // Percentuali maggiorazione/sconto
            $table->decimal('percentuale_notturno', 5, 2)->default(0); // es: 25.00 per +25%
            $table->decimal('percentuale_festivo', 5, 2)->default(0);
            $table->decimal('sconto_percentuale', 5, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impostazioni_fattura');
    }
};
