<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documenti', function (Blueprint $table) {
            // SOLO UN CAMPO TEXT per tutto quello che riconosce ChatGPT
            $table->text('ai_testo_estratto')->nullable()->after('file')->comment('Tutto il testo riconosciuto da ChatGPT');
            $table->boolean('ai_processato')->default(false)->after('ai_testo_estratto')->comment('Se processato da AI');
            $table->timestamp('ai_processato_at')->nullable()->after('ai_processato')->comment('Quando processato');
        });
    }

    public function down(): void
    {
        Schema::table('documenti', function (Blueprint $table) {
            $table->dropColumn([
                'ai_testo_estratto',
                'ai_processato', 
                'ai_processato_at'
            ]);
        });
    }
};
