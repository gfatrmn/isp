<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nik')->nullable()->after('customer_number');
            $table->string('ktp_image')->nullable()->after('nik');
            $table->string('house_image')->nullable()->after('ktp_image');
            $table->string('district')->nullable()->after('address'); // Kecamatan
            $table->string('village')->nullable()->after('district'); // Desa
            $table->text('google_maps_url')->nullable()->after('village'); // Link Map / Koordinat
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['nik', 'ktp_image', 'house_image', 'district', 'village', 'google_maps_url']);
        });
    }
};
