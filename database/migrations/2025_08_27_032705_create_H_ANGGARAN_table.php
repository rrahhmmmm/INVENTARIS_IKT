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
        Schema::create('H_ANGGARAN', function (Blueprint $table) {
            $table->integer('IDH_ANGGARAN', true);
            $table->integer('ID_ANGGARAN');
            $table->enum('nama_anggaran', ['opex', 'capex']);
            $table->date('tahun_anggaran')->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('H_ANGGARAN');
    }
};
