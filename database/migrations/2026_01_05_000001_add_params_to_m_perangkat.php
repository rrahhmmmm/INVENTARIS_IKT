<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan param4-param16 ke M_PERANGKAT untuk menyimpan nama field dinamis
     */
    public function up(): void
    {
        Schema::table('M_PERANGKAT', function (Blueprint $table) {
            $table->string('param4', 100)->nullable()->after('param3');
            $table->string('param5', 100)->nullable()->after('param4');
            $table->string('param6', 100)->nullable()->after('param5');
            $table->string('param7', 100)->nullable()->after('param6');
            $table->string('param8', 100)->nullable()->after('param7');
            $table->string('param9', 100)->nullable()->after('param8');
            $table->string('param10', 100)->nullable()->after('param9');
            $table->string('param11', 100)->nullable()->after('param10');
            $table->string('param12', 100)->nullable()->after('param11');
            $table->string('param13', 100)->nullable()->after('param12');
            $table->string('param14', 100)->nullable()->after('param13');
            $table->string('param15', 100)->nullable()->after('param14');
            $table->string('param16', 100)->nullable()->after('param15');
        });

        // Seed field schema untuk perangkat yang sudah ada
        // PC/Laptop
        DB::table('M_PERANGKAT')
            ->where('KODE_PERANGKAT', 'PC')
            ->update([
                'param1' => 'SERIAL NUMBER',
                'param2' => 'PROCESSOR',
                'param3' => 'RAM',
                'param4' => 'STORAGE',
                'param5' => 'SISTEM OPERASI',
                'param6' => 'USER',
                'param7' => 'KETERANGAN',
            ]);

        // Printer
        DB::table('M_PERANGKAT')
            ->where('KODE_PERANGKAT', 'PRINTER')
            ->update([
                'param1' => 'JENIS',
                'param2' => 'SPESIFIKASI',
                'param3' => 'SERIAL NUMBER',
                'param4' => 'USER',
                'param5' => 'KETERANGAN',
            ]);

        // CCTV
        DB::table('M_PERANGKAT')
            ->where('KODE_PERANGKAT', 'CCTV')
            ->update([
                'param1' => 'IP ADDRESS',
                'param2' => 'AREA',
                'param3' => 'TITIK LOKASI',
                'param4' => 'JENIS KAMERA',
                'param5' => 'KETERANGAN',
            ]);

        // Handheld
        DB::table('M_PERANGKAT')
            ->where('KODE_PERANGKAT', 'HENHEL')
            ->update([
                'param1' => 'IMEI',
                'param2' => 'SERIAL NUMBER',
                'param3' => 'AREA',
                'param4' => 'DEVICE NAME',
                'param5' => 'MODEL',
                'param6' => 'KETERANGAN',
            ]);

        // Access Point
        DB::table('M_PERANGKAT')
            ->where('KODE_PERANGKAT', 'AP')
            ->update([
                'param1' => 'SSID',
                'param2' => 'AP GROUP',
                'param3' => 'IP ADDRESS',
                'param4' => 'STATUS AP',
                'param5' => 'KETERANGAN',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('M_PERANGKAT', function (Blueprint $table) {
            $table->dropColumn([
                'param4', 'param5', 'param6', 'param7', 'param8',
                'param9', 'param10', 'param11', 'param12', 'param13',
                'param14', 'param15', 'param16'
            ]);
        });

        // Reset param1-3 to null
        DB::table('M_PERANGKAT')->update([
            'param1' => null,
            'param2' => null,
            'param3' => null,
        ]);
    }
};
