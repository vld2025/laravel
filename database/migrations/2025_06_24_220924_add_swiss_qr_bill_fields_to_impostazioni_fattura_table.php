<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('impostazioni_fattura', function (Blueprint $table) {
            // Campi per Swiss QR Bill
            $table->string('qr_creditor_name')->nullable()->after('iban');
            $table->string('qr_creditor_address')->nullable()->after('qr_creditor_name');
            $table->string('qr_creditor_postal_code')->nullable()->after('qr_creditor_address');
            $table->string('qr_creditor_city')->nullable()->after('qr_creditor_postal_code');
            $table->string('qr_creditor_country', 2)->default('CH')->after('qr_creditor_city');
            $table->text('qr_additional_info')->nullable()->after('qr_creditor_country');
            $table->text('qr_billing_info')->nullable()->after('qr_additional_info');
        });
    }

    public function down(): void
    {
        Schema::table('impostazioni_fattura', function (Blueprint $table) {
            $table->dropColumn([
                'qr_creditor_name',
                'qr_creditor_address', 
                'qr_creditor_postal_code',
                'qr_creditor_city',
                'qr_creditor_country',
                'qr_additional_info',
                'qr_billing_info'
            ]);
        });
    }
};
