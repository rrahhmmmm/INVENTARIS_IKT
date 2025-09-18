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
        Schema::create('H_M_DIVISI', function (Blueprint $table) {
            $table->integer('IDH_DIVSI', true);
            $table->integer('ID_DIVISI')->nullable();
            $table->string('NAMA_DIVISI', 100);
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('STATUS')->default();
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
        Schema::dropIfExists('H_M_DIVISI');
    }
};
