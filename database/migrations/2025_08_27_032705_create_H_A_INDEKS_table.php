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
        Schema::create('H_M_INDEKS', function (Blueprint $table) {
            $table->integer('IDH_INDEKS', true);
            $table->integer('ID_INDEKS')->nullable();
            $table->string('NO_INDEKS', 100);
            $table->string('WILAYAH', 100);
            $table->date('START_DATE')->nullable();
            $table->date('END_DATE')->nullable();
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
        Schema::dropIfExists('H_A_INDEKS');
    }
};
