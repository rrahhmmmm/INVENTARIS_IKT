<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * - Hapus kolom legacy (detail perangkat lama)
     * - Ubah param1-3 ke text
     * - Tambah param4-param16
     */
    public function up(): void
    {
        // Hapus semua data inventaris yang ada (fresh start)
        DB::table('T_INVENTARIS')->truncate();

        Schema::table('T_INVENTARIS', function (Blueprint $table) {
            // Hapus kolom legacy
            $table->dropColumn([
                'SERIAL_NUMBER',
                'KAPASITAS_PROSESSOR',
                'MEMORI_UTAMA',
                'KAPASITAS_PENYIMPANAN',
                'SISTEM_OPERASI',
                'USER_PENANGGUNG',
                'ID_INSTAL',
                'KETERANGAN',
                'KETERANGAN_ASSET',
                'DETAIL_PERANGKAT',
            ]);
        });

        Schema::table('T_INVENTARIS', function (Blueprint $table) {
            // Hapus param1-3 lama (varchar) dan buat ulang sebagai text
            $table->dropColumn(['param1', 'param2', 'param3']);
        });

        Schema::table('T_INVENTARIS', function (Blueprint $table) {
            // Tambah param1-param16 sebagai text
            $table->text('param1')->nullable()->after('STATUS');
            $table->text('param2')->nullable()->after('param1');
            $table->text('param3')->nullable()->after('param2');
            $table->text('param4')->nullable()->after('param3');
            $table->text('param5')->nullable()->after('param4');
            $table->text('param6')->nullable()->after('param5');
            $table->text('param7')->nullable()->after('param6');
            $table->text('param8')->nullable()->after('param7');
            $table->text('param9')->nullable()->after('param8');
            $table->text('param10')->nullable()->after('param9');
            $table->text('param11')->nullable()->after('param10');
            $table->text('param12')->nullable()->after('param11');
            $table->text('param13')->nullable()->after('param12');
            $table->text('param14')->nullable()->after('param13');
            $table->text('param15')->nullable()->after('param14');
            $table->text('param16')->nullable()->after('param15');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('T_INVENTARIS', function (Blueprint $table) {
            // Hapus param1-16
            $table->dropColumn([
                'param1', 'param2', 'param3', 'param4', 'param5', 'param6',
                'param7', 'param8', 'param9', 'param10', 'param11', 'param12',
                'param13', 'param14', 'param15', 'param16'
            ]);
        });

        Schema::table('T_INVENTARIS', function (Blueprint $table) {
            // Kembalikan kolom legacy
            $table->string('SERIAL_NUMBER', 100)->nullable();
            $table->string('KAPASITAS_PROSESSOR', 100)->nullable();
            $table->string('MEMORI_UTAMA', 50)->nullable();
            $table->string('KAPASITAS_PENYIMPANAN', 50)->nullable();
            $table->string('SISTEM_OPERASI', 100)->nullable();
            $table->string('USER_PENANGGUNG', 100)->nullable();
            $table->integer('ID_INSTAL')->nullable();
            $table->text('KETERANGAN')->nullable();
            $table->text('KETERANGAN_ASSET')->nullable();
            $table->json('DETAIL_PERANGKAT')->nullable();
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });
    }
};
