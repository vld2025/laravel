<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documenti', function (Blueprint $table) {
            $table->boolean('elaborazione_ocr')->default(true)->after('ai_processato_at');
            $table->boolean('ritaglio_automatico')->default(true)->after('elaborazione_ocr');
        });
    }

    public function down(): void
    {
        Schema::table('documenti', function (Blueprint $table) {
            $table->dropColumn(['elaborazione_ocr', 'ritaglio_automatico']);
        });
    }
};
