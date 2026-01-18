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
        Schema::table('T_INVENTARIS', function (Blueprint $table) {
            // Add ID_PERANGKAT column after ID_TERMINAL with default 1 (PC/Laptop)
            $table->integer('ID_PERANGKAT')->default(1)->after('ID_TERMINAL');

            // Add JSON column for device-specific fields
            $table->json('DETAIL_PERANGKAT')->nullable()->after('KETERANGAN_ASSET');
        });

        // Migrate existing PC/Laptop data to JSON format
        // This preserves backward compatibility while moving to new structure
        DB::statement("
            UPDATE T_INVENTARIS
            SET ID_PERANGKAT = 1,
                DETAIL_PERANGKAT = JSON_OBJECT(
                    'SERIAL_NUMBER', COALESCE(SERIAL_NUMBER, ''),
                    'KAPASITAS_PROSESSOR', COALESCE(KAPASITAS_PROSESSOR, ''),
                    'MEMORI_UTAMA', COALESCE(MEMORI_UTAMA, ''),
                    'KAPASITAS_PENYIMPANAN', COALESCE(KAPASITAS_PENYIMPANAN, ''),
                    'SISTEM_OPERASI', COALESCE(SISTEM_OPERASI, ''),
                    'USER_PENANGGUNG', COALESCE(USER_PENANGGUNG, ''),
                    'ID_INSTAL', ID_INSTAL,
                    'KETERANGAN', COALESCE(KETERANGAN, ''),
                    'KETERANGAN_ASSET', COALESCE(KETERANGAN_ASSET, '')
                )
            WHERE ID_PERANGKAT = 1 OR ID_PERANGKAT IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('T_INVENTARIS', function (Blueprint $table) {
            $table->dropColumn(['ID_PERANGKAT', 'DETAIL_PERANGKAT']);
        });
    }
};
