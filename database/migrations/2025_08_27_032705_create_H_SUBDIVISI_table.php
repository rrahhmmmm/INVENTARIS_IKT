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
        Schema::create('H_SUBDIVISI', function (Blueprint $table) {
            $table->integer('IDH_SUBDIVISI', true);
            $table->integer('ID_SUBDIVISI')->nullable();
            $table->integer('ID_DIVIS')->nullable();
            $table->string('NAMA_SUBDIVISI', 100);
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('STATUS')->nullable();
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
        Schema::dropIfExists('H_SUBDIVISI');
    }
};
