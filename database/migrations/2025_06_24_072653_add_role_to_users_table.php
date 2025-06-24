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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'user'])->default('user');
            $table->string('telefono')->nullable();
            $table->text('indirizzo')->nullable();
            $table->string('taglia_giacca')->nullable();
            $table->string('taglia_pantaloni')->nullable();
            $table->string('taglia_maglietta')->nullable();
            $table->string('taglia_scarpe')->nullable();
            $table->text('note_abbigliamento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'telefono',
                'indirizzo',
                'taglia_giacca',
                'taglia_pantaloni',
                'taglia_maglietta',
                'taglia_scarpe',
                'note_abbigliamento'
            ]);
        });
    }
};
