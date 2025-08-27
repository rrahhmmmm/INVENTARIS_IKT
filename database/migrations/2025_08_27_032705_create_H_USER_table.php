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
        Schema::create('H_USER', function (Blueprint $table) {
            $table->integer('IDH_USER', true);
            $table->integer('ID_USER');
            $table->string('username', 100);
            $table->string('password');
            $table->string('email', 150)->nullable();
            $table->string('full_name', 150)->nullable();
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('status');
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
        Schema::dropIfExists('H_USER');
    }
};
