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
        Schema::create('M_RETENSI', function (Blueprint $table) {
            $table->integer('ID_RETENSI', true);
            $table->string('JENIS_ARSIP', 150);
            $table->string('BIDANG_ARSIP', 150);
            $table->string('TIPE_ARSIP', 150);
            $table->string('DETAIL_TIPE_ARSIP')->nullable();
            $table->integer('MASA_AKTIF');
            $table->string('DESC_AKTIF',150)->nullable();
            $table->integer('MASA_INAKTIF');
            $table->string('DESC_INAKTIF',150)->nullable();
            $table->text('KETERANGAN')->nullable();
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->default(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_insert_retensi
            AFTER INSERT ON M_RETENSI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_RETENSI
                (ID_RETENSI, JENIS_ARSIP, BIDANG_ARSIP, TIPE_ARSIP, DETAIL_TIPE_ARSIP,
                 MASA_AKTIF, DESC_AKTIF, MASA_INAKTIF, DESC_INAKTIF, KETERANGAN,
                 CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
                 STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_RETENSI, NEW.JENIS_ARSIP, NEW.BIDANG_ARSIP, NEW.TIPE_ARSIP, NEW.DETAIL_TIPE_ARSIP,
                 NEW.MASA_AKTIF, NEW.DESC_AKTIF, NEW.MASA_INAKTIF, NEW.DESC_INAKTIF, NEW.KETERANGAN,
                 NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
                 NEW.STATUS, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_update_retensi
            AFTER UPDATE ON M_RETENSI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_RETENSI
                (ID_RETENSI, JENIS_ARSIP, BIDANG_ARSIP, TIPE_ARSIP, DETAIL_TIPE_ARSIP,
                 MASA_AKTIF, DESC_AKTIF, MASA_INAKTIF, DESC_INAKTIF, KETERANGAN,
                 CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
                 STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_RETENSI, NEW.JENIS_ARSIP, NEW.BIDANG_ARSIP, NEW.TIPE_ARSIP, NEW.DETAIL_TIPE_ARSIP,
                 NEW.MASA_AKTIF, NEW.DESC_AKTIF, NEW.MASA_INAKTIF, NEW.DESC_INAKTIF, NEW.KETERANGAN,
                 NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
                 2, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger BEFORE UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_status_retensi
            BEFORE UPDATE ON M_RETENSI
            FOR EACH ROW
            BEGIN
                SET NEW.STATUS = 2;
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_delete_retensi
            AFTER DELETE ON M_RETENSI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_RETENSI
                (ID_RETENSI, JENIS_ARSIP, BIDANG_ARSIP, TIPE_ARSIP, DETAIL_TIPE_ARSIP,
                 MASA_AKTIF, DESC_AKTIF, MASA_INAKTIF, DESC_INAKTIF, KETERANGAN,
                 CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
                 STATUS, param1, param2, param3)
                VALUES
                (OLD.ID_RETENSI, OLD.JENIS_ARSIP, OLD.BIDANG_ARSIP, OLD.TIPE_ARSIP, OLD.DETAIL_TIPE_ARSIP,
                 OLD.MASA_AKTIF, OLD.DESC_AKTIF, OLD.MASA_INAKTIF, OLD.DESC_INAKTIF, OLD.KETERANGAN,
                 OLD.CREATE_BY, OLD.CREATE_DATE, OLD.UPDATE_BY, OLD.UPDATE_DATE,
                 99, OLD.param1, OLD.param2, OLD.param3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_insert_retensi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_retensi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_delete_retensi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_retensi');
        Schema::dropIfExists('M_RETENSI');
    }
};
