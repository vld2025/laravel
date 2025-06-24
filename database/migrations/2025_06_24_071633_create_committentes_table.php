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
        Schema::create('committenti', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // obbligatorio
            $table->string('partita_iva')->nullable();
            $table->text('indirizzo')->nullable();
            $table->string('iban')->nullable();
            $table->string('logo')->nullable(); // path file upload
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
        Schema::dropIfExists('committenti');
    }
};
