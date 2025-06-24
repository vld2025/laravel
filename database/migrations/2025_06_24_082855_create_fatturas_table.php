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
        Schema::create('fatture', function (Blueprint $table) {
            $table->id();
            
            // Identificativi
            $table->string('numero')->unique(); // numero fattura univoco
            $table->foreignId('committente_id')->constrained('committenti')->onDelete('cascade');
            
            // Date
            $table->date('data_emissione');
            $table->integer('mese_riferimento'); // 1-12
            $table->integer('anno_riferimento');
            
            // Stato fattura
            $table->enum('stato', ['bozza', 'emessa', 'pagata'])->default('bozza');
            
            // Totali quantità
            $table->decimal('totale_ore_lavoro', 10, 2)->default(0);
            $table->decimal('totale_ore_viaggio', 10, 2)->default(0);
            $table->integer('totale_km')->default(0);
            $table->integer('totale_pranzi')->default(0);
            $table->integer('totale_trasferte')->default(0);
            $table->decimal('totale_spese_extra', 10, 2)->default(0);
            
            // Importi
            $table->decimal('imponibile', 10, 2)->default(0);
            $table->decimal('sconto', 10, 2)->default(0);
            $table->decimal('totale', 10, 2)->default(0);
            
            // Dati aggiuntivi
            $table->json('dettagli')->nullable(); // dettaglio calcoli
            $table->string('pdf_path')->nullable(); // path del PDF generato
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['committente_id', 'anno_riferimento', 'mese_riferimento']);
            $table->index(['stato', 'data_emissione']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatture');
    }
};
