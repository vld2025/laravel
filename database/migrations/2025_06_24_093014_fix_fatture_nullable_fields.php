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
        Schema::table('fatture', function (Blueprint $table) {
            $table->decimal('totale_ore_lavoro', 10, 2)->nullable()->change();
            $table->decimal('totale_ore_viaggio', 10, 2)->nullable()->change();
            $table->integer('totale_km')->nullable()->change();
            $table->integer('totale_pranzi')->nullable()->change();
            $table->integer('totale_trasferte')->nullable()->change();
            $table->decimal('totale_spese_extra', 10, 2)->nullable()->change();
            $table->decimal('imponibile', 10, 2)->nullable()->change();
            $table->decimal('sconto', 10, 2)->nullable()->change();
            $table->decimal('totale', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fatture', function (Blueprint $table) {
            $table->decimal('totale_ore_lavoro', 10, 2)->default(0)->change();
            $table->decimal('totale_ore_viaggio', 10, 2)->default(0)->change();
            $table->integer('totale_km')->default(0)->change();
            $table->integer('totale_pranzi')->default(0)->change();
            $table->integer('totale_trasferte')->default(0)->change();
            $table->decimal('totale_spese_extra', 10, 2)->default(0)->change();
            $table->decimal('imponibile', 10, 2)->default(0)->change();
            $table->decimal('sconto', 10, 2)->default(0)->change();
            $table->decimal('totale', 10, 2)->default(0)->change();
        });
    }
};
