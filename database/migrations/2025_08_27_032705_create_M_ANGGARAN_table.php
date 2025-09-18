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
        Schema::create('M_ANGGARAN', function (Blueprint $table) {
            $table->integer('ID_ANGGARAN', true);
            $table->enum('nama_anggaran', ['opex', 'capex']);
            $table->integer('tahun_anggaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('status')->default(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_anggaran_delete 
            AFTER DELETE ON M_ANGGARAN 
            FOR EACH ROW 
            BEGIN
                INSERT INTO H_M_ANGGARAN 
                (ID_ANGGARAN, nama_anggaran, tahun_anggaran, keterangan, create_by, create_date, update_by, update_date, status, param1, param2, param3)
                VALUES 
                (OLD.ID_ANGGARAN, OLD.nama_anggaran, OLD.tahun_anggaran, OLD.keterangan, OLD.create_by, OLD.create_date, OLD.update_by, OLD.update_date, 99, OLD.param1, OLD.param2, OLD.param3);
            END
        ');

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_anggaran_insert 
            AFTER INSERT ON M_ANGGARAN 
            FOR EACH ROW 
            BEGIN
                INSERT INTO H_M_ANGGARAN 
                (ID_ANGGARAN, nama_anggaran, tahun_anggaran, keterangan, create_by, create_date, update_by, update_date, status, param1, param2, param3)
                VALUES 
                (NEW.ID_ANGGARAN, NEW.nama_anggaran, NEW.tahun_anggaran, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date, NEW.status, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger BEFORE UPDATE (set status = 2)
        DB::unprepared('
            CREATE TRIGGER trg_status_anggaran
            BEFORE UPDATE ON M_ANGGARAN
            FOR EACH ROW
            BEGIN
                SET NEW.status = 2;
            END
        ');

        // Trigger AFTER UPDATE (catat ke history dengan status = 2)
        DB::unprepared('
            CREATE TRIGGER trg_m_anggaran_update 
            AFTER UPDATE ON M_ANGGARAN 
            FOR EACH ROW 
            BEGIN
                INSERT INTO H_M_ANGGARAN 
                (ID_ANGGARAN, nama_anggaran, tahun_anggaran, keterangan, create_by, create_date, update_by, update_date, status, param1, param2, param3)
                VALUES 
                (NEW.ID_ANGGARAN, NEW.nama_anggaran, NEW.tahun_anggaran, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date, 2, NEW.param1, NEW.param2, NEW.param3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_anggaran_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_anggaran_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_anggaran_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_anggaran');

        // Drop table
        Schema::dropIfExists('M_ANGGARAN');
    }
};
