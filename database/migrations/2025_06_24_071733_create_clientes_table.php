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
        Schema::create('clienti', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // obbligatorio
            $table->foreignId('committente_id')->constrained('committenti')->onDelete('cascade'); // relazione obbligatoria
            $table->text('indirizzo')->nullable();
            $table->json('dati_bancari')->nullable();
            $table->text('informazioni')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clienti');
    }
};
