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
        Schema::create('H_M_KLASIFIKASI', function (Blueprint $table) {
            $table->integer('IDH_KLASIFIKASI', true);
            $table->integer('ID_KLASIFIKASI')->nullable();
            $table->string('KODE_KLASIFIKASI', 100);
            $table->string('KATEGORI', 100);
            $table->string('DESKRIPSI',1000);
            $table->date('START_DATE')->nullable();
            $table->date('END_DATE')->nullable();
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->nullable();
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
        Schema::dropIfExists('H_M_KLASIFIKASI');
    }
};
