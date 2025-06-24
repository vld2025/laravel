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
        Schema::create('documenti', function (Blueprint $table) {
            $table->id();
            
            // Relazioni
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // nullable per doc aziendali
            $table->foreignId('caricato_da')->constrained('users')->onDelete('cascade'); // chi ha caricato il documento
            
            // Dati documento
            $table->enum('tipo', ['busta_paga', 'personale', 'aziendale']);
            $table->string('nome'); // nome del documento
            $table->string('file'); // path del file upload
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['user_id', 'tipo']);
            $table->index(['tipo', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documenti');
    }
};
