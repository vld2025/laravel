<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pdf_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('descrizione')->nullable();
            $table->string('tipo')->default('report'); // report, fattura, spese, etc.
            $table->longText('template_html');
            $table->longText('css_personalizzato')->nullable();
            $table->json('variabili_disponibili')->nullable();
            $table->boolean('attivo')->default(true);
            $table->string('orientamento')->default('portrait'); // portrait o landscape
            $table->string('formato_pagina')->default('A4');
            $table->json('margini')->nullable(); // top, right, bottom, left
            $table->timestamps();
        });

        // Inserisci il template di default per i report
        DB::table('pdf_templates')->insert([
            'nome' => 'report_giornalieri_default',
            'descrizione' => 'Template predefinito per report giornalieri',
            'tipo' => 'report',
            'template_html' => file_get_contents(resource_path('views/pdf/report-giornalieri.blade.php')),
            'variabili_disponibili' => json_encode([
                'data' => 'Data del report',
                'identificativo' => 'Nome tecnico o TUTTI I TECNICI',
                'reports' => 'Collezione dei report',
                'includi_dettagli_ore' => 'Flag per mostrare ore e km',
                'reportContent' => 'Contenuti generati da AI'
            ]),
            'attivo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pdf_templates');
    }
};
