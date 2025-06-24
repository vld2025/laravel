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
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            
            // Relazioni obbligatorie
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('data')->default(now());
            $table->foreignId('committente_id')->constrained('committenti')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clienti')->onDelete('cascade');
            $table->foreignId('commessa_id')->constrained('commesse')->onDelete('cascade');
            
            // Ore lavorate (decimal 3,1, range 0-24, step 0.5)
            $table->decimal('ore_lavorate', 3, 1);
            $table->decimal('ore_viaggio', 3, 1);
            
            // Chilometri e auto
            $table->integer('km_auto')->default(0);
            $table->boolean('auto_privata')->default(false); // false = aziendale
            
            // Flags lavoro
            $table->boolean('notturno')->default(false);
            $table->boolean('trasferta')->default(false);
            
            // Descrizioni lavori
            $table->text('descrizione_lavori')->nullable();
            $table->text('descrizione_it')->nullable(); // generato da AI
            $table->text('descrizione_en')->nullable(); // generato da AI
            $table->text('descrizione_de')->nullable(); // generato da AI
            $table->text('descrizione_ru')->nullable(); // generato da AI
            
            // Ore fatturazione (modificabili da Manager/Admin)
            $table->decimal('ore_lavorate_fatturazione', 3, 1)->nullable();
            $table->decimal('ore_viaggio_fatturazione', 3, 1)->nullable();
            
            // Flag fatturazione
            $table->boolean('fatturato')->default(false);
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['user_id', 'data']);
            $table->index(['committente_id', 'data']);
            $table->index('fatturato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
