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
        Schema::create('H_M_BARANG', function (Blueprint $table) {
            $table->integer('IDH_BARANG', true);
            $table->integer('ID_BARANG');
            $table->string('KODE_BARANG', 20);
            $table->string('NAMA_BARANG', 100);
            $table->integer('ID_TIPE');
            $table->integer('ID_STATUS');
            $table->integer('ID_LOKASI');
            $table->integer('ID_ANGGARAN');
            $table->integer('ID_PARAMETER');
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('status');
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('H_BARANG');
    }
};
