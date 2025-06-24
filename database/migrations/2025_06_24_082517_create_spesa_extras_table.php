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
        Schema::create('spese_extra', function (Blueprint $table) {
            $table->id();
            
            // Relazioni
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('committente_id')->constrained('committenti')->onDelete('cascade');
            
            // Dati spesa
            $table->string('file'); // path del file upload
            $table->decimal('importo', 10, 2)->nullable(); // estratto da AI
            $table->text('descrizione')->nullable(); // estratta da AI
            $table->date('data');
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['user_id', 'data']);
            $table->index(['committente_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spese_extra');
    }
};
