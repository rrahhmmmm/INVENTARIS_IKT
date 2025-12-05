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
        Schema::create('T_ARSIP', function (Blueprint $table) {
            $table->integer('ID_ARSIP', true);
            $table->integer('ID_DIVISI');
            $table->integer('ID_SUBDIVISI');
            $table->string('NO_INDEKS', 100);
            $table->integer('NO_BERKAS');
            $table->string('JUDUL_BERKAS',256);
            $table->integer('NO_ISI_BERKAS');
            $table->string('JENIS_ARSIP',100);
            $table->string('KODE_KLASIFIKASI',100);
            $table->string('NO_NOTA_DINAS', 100)->unique();
            $table->date('TANGGAL_BERKAS');
            $table->string('PERIHAL',100);
            $table->string('TINGKAT_PENGEMBANGAN', 50);
            $table->string('KONDISI', 100)->nullable();
            $table->string('RAK_BAK_URUTAN',50);
            $table->string('KETERANGAN_SIMPAN', 15);
            $table->string('TIPE_RETENSI', 150)->nullable();
            $table->date('TANGGAL_RETENSI')->nullable();
            $table->string('MASA_INAKTIF')->nullable,50();
            $table->date('TANGGAL_INAKTIF')->nullable();
            $table->string('KETERANGAN_INAKTIF')->nullable();
            $table->string('KETERANGAN', 255)->nullable();
            $table->string('FILE',255)->nullable();
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->string('KETERANGAN_UPDATE', 255)->nullable();
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
        Schema::dropIfExists('T_ARSIP');
    }
};
