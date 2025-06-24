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
        Schema::create('commesse', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // obbligatorio
            $table->text('descrizione')->nullable();
            $table->foreignId('cliente_id')->constrained('clienti')->onDelete('cascade'); // relazione obbligatoria
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commesse');
    }
};
