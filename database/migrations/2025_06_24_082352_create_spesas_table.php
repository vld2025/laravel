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
        Schema::create('spese', function (Blueprint $table) {
            $table->id();
            
            // Relazioni
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Data spesa
            $table->integer('anno')->default(date('Y'));
            $table->integer('mese')->default(date('n'));
            
            // File e descrizione
            $table->string('file'); // path del file upload
            $table->string('descrizione')->nullable();
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['user_id', 'anno', 'mese']);
            $table->unique(['user_id', 'anno', 'mese', 'file']); // evita duplicati
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spese');
    }
};
