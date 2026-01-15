<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('M_PERANGKAT', function (Blueprint $table) {
            $table->integer('ID_PERANGKAT', true);
            $table->string('NAMA_PERANGKAT', 100);
            $table->string('KODE_PERANGKAT', 20);
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->default(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });

        // Seed default device types
        DB::table('M_PERANGKAT')->insert([
            [
                'NAMA_PERANGKAT' => 'PC/Laptop',
                'KODE_PERANGKAT' => 'PC',
                'CREATE_BY' => 'system',
                'STATUS' => 1
            ],
            [
                'NAMA_PERANGKAT' => 'Printer & Scan',
                'KODE_PERANGKAT' => 'PRINTER',
                'CREATE_BY' => 'system',
                'STATUS' => 1
            ],
            [
                'NAMA_PERANGKAT' => 'CCTV',
                'KODE_PERANGKAT' => 'CCTV',
                'CREATE_BY' => 'system',
                'STATUS' => 1
            ],
            [
                'NAMA_PERANGKAT' => 'Handheld (HENHEL)',
                'KODE_PERANGKAT' => 'HENHEL',
                'CREATE_BY' => 'system',
                'STATUS' => 1
            ],
            [
                'NAMA_PERANGKAT' => 'AP (Access Point)',
                'KODE_PERANGKAT' => 'AP',
                'CREATE_BY' => 'system',
                'STATUS' => 1
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('M_PERANGKAT');
    }
};
