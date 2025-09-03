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
        Schema::create('H_M_TERMINAL', function (Blueprint $table) {
            $table->integer('IDH_terminal', true);
            $table->integer('ID_terminal');
            $table->string('KODE_terminal', 20);
            $table->string('NAMA_terminal', 20);
            $table->string('LOKASI', 20);
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('status');
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
        Schema::dropIfExists('H_TERMINAL');
    }
};
