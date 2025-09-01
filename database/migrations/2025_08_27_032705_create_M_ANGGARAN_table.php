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
            $table->DATE('tahun_anggaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('status');
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
        });

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_anggaran_delete 
            AFTER DELETE ON M_ANGGARAN 
            FOR EACH ROW 
            BEGIN
                INSERT INTO H_M_ANGGARAN 
                (ID_ANGGARAN, nama_anggaran, tahun_anggaran, keterangan, create_by, create_date, update_by, update_date, status, attr1, attr2, attr3)
                VALUES 
                (OLD.ID_ANGGARAN, OLD.nama_anggaran, OLD.tahun_anggaran, OLD.keterangan, OLD.create_by, OLD.create_date, OLD.update_by, OLD.update_date, OLD.status, OLD.attr1, OLD.attr2, OLD.attr3);
            END
        ');

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_anggaran_insert 
            AFTER INSERT ON M_ANGGARAN 
            FOR EACH ROW 
            BEGIN
                INSERT INTO H_M_ANGGARAN 
                (ID_ANGGARAN, nama_anggaran, tahun_anggaran, keterangan, create_by, create_date, update_by, update_date, status, attr1, attr2, attr3)
                VALUES 
                (NEW.ID_ANGGARAN, NEW.nama_anggaran, NEW.tahun_anggaran, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date, NEW.status, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_anggaran_update 
            AFTER UPDATE ON M_ANGGARAN 
            FOR EACH ROW 
            BEGIN
                INSERT INTO H_M_ANGGARAN 
                (ID_ANGGARAN, nama_anggaran, tahun_anggaran, keterangan, create_by, create_date, update_by, update_date, status, attr1, attr2, attr3)
                VALUES 
                (NEW.ID_ANGGARAN, NEW.nama_anggaran, NEW.tahun_anggaran, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date, NEW.status, NEW.attr1, NEW.attr2, NEW.attr3);
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

        // Drop table
        Schema::dropIfExists('M_ANGGARAN');
    }
};
