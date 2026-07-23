<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: ODP-PSN-001
            $table->string('name'); // Contoh: ODP Banaran 01
            $table->integer('capacity')->default(8); // Kapasitas Total Port (misal: 8, 16, 24)
            $table->text('address');
            $table->string('district')->nullable(); // Kecamatan
            $table->string('village')->nullable(); // Desa
            $table->text('google_maps_url')->nullable(); // Link lokasi Google Maps
            $table->string('odp_image')->nullable(); // Foto dokumentasi fisik ODP
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};
