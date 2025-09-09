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
        Schema::create('M_RETENSI', function (Blueprint $table) {
            $table->integer('ID_RETENSI', true);
            $table->string('jenis_arsip', 150);
            $table->string('bidang_arsip', 150);
            $table->string('tipe_arsip', 150);
            $table->string('detail_tipe_arsip')->nullable();
            $table->integer('masa_aktif');
            $table->string('DESC_AKTIF',150)->nullable();
            $table->integer('masa_inaktif');
            $table->string('DESC_INAKTIF',150)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->default(1);
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
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
         create_by, create_date, update_by, update_date,
         status, attr1, attr2, attr3)
        VALUES
        (NEW.ID_RETENSI, NEW.JENIS_ARSIP, NEW.BIDANG_ARSIP, NEW.TIPE_ARSIP, NEW.DETAIL_TIPE_ARSIP,
         NEW.MASA_AKTIF, NEW.DESC_AKTIF, NEW.MASA_INAKTIF, NEW.DESC_INAKTIF, NEW.KETERANGAN,
         NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date,
         NEW.status, NEW.attr1, NEW.attr2, NEW.attr3);
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
         create_by, create_date, update_by, update_date,
         status, attr1, attr2, attr3)
        VALUES
        (NEW.ID_RETENSI, NEW.JENIS_ARSIP, NEW.BIDANG_ARSIP, NEW.TIPE_ARSIP, NEW.DETAIL_TIPE_ARSIP,
         NEW.MASA_AKTIF, NEW.DESC_AKTIF, NEW.MASA_INAKTIF, NEW.DESC_INAKTIF, NEW.KETERANGAN,
         NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date,
         2, NEW.attr1, NEW.attr2, NEW.attr3);
    END
    ');

    // Trigger BEFORE UPDATE
    DB::unprepared('
    CREATE TRIGGER trg_status_retensi
    BEFORE UPDATE ON M_RETENSI
    FOR EACH ROW
    BEGIN
        SET NEW.status = 2;
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
         create_by, create_date, update_by, update_date,
         status, attr1, attr2, attr3)
        VALUES
        (OLD.ID_RETENSI, OLD.JENIS_ARSIP, OLD.BIDANG_ARSIP, OLD.TIPE_ARSIP, OLD.DETAIL_TIPE_ARSIP,
         OLD.MASA_AKTIF, OLD.DESC_AKTIF, OLD.MASA_INAKTIF, OLD.DESC_INAKTIF, OLD.KETERANGAN,
         OLD.create_by, OLD.create_date, OLD.update_by, OLD.update_date,
         99, OLD.attr1, OLD.attr2, OLD.attr3);
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
