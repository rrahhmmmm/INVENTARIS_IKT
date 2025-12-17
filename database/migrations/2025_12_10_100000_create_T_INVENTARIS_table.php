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
        Schema::create('T_INVENTARIS', function (Blueprint $table) {
            $table->integer('ID_INVENTARIS', true);
            $table->integer('ID_TERMINAL');
            $table->integer('ID_MERK')->nullable();
            $table->string('TIPE', 100)->nullable();
            $table->string('SERIAL_NUMBER', 100)->nullable();
            $table->string('TAHUN_PENGADAAN', 4)->nullable();
            $table->string('KAPASITAS_PROSESSOR', 100)->nullable();
            $table->string('MEMORI_UTAMA', 50)->nullable();
            $table->string('KAPASITAS_PENYIMPANAN', 50)->nullable();
            $table->string('SISTEM_OPERASI', 100)->nullable();
            $table->string('USER_PENANGGUNG', 100)->nullable();
            $table->string('LOKASI_POSISI', 150)->nullable();
            $table->integer('ID_KONDISI')->nullable();
            $table->text('KETERANGAN')->nullable();
            $table->integer('ID_INSTAL')->nullable();
            $table->integer('ID_ANGGARAN')->nullable();
            $table->text('KETERANGAN_ASSET')->nullable();
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->default(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('T_INVENTARIS');
    }
};
