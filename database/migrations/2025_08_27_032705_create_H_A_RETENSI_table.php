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
        Schema::create('H_M_RETENSI', function (Blueprint $table) {
            $table->integer('IDH_RETENSI', true);
            $table->integer('ID_RETENSI')->nullable();
            $table->string('jenis_arsip', 150);
            $table->string('bidang_arsip', 150);
            $table->string('tipe_arsip', 150);
            $table->string('detail_tipe_arsip')->nullable();
            $table->text('masa_aktif');
            $table->string('DESC_AKTIF',150)->nullable();
            $table->integer('masa_inaktif');
            $table->string('DESC_INAKTIF',150)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->nullable();
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
        Schema::dropIfExists('H_A_RETENSI');
    }
};
