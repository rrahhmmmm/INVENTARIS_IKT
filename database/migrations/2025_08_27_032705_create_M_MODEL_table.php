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
        // Tabel master
        Schema::create('M_MODEL', function (Blueprint $table) {
            $table->integer('ID_MODEL', true);
            $table->string('NAMA_MODEL', 50);
            $table->string('KETERANGAN', 200)->nullable();
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('status')->default(1);
            $table->string('attr1', 100)->nullable();
            $table->string('attr2', 100)->nullable();
            $table->string('attr3', 100)->nullable();
        });


        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_model_insert
            AFTER INSERT ON M_MODEL
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_MODEL
                (ID_MODEL, NAMA_MODEL, KETERANGAN,
                 create_by, create_date, update_by, update_date, status,
                 attr1, attr2, attr3)
                VALUES
                (NEW.ID_MODEL, NEW.NAMA_MODEL, NEW.KETERANGAN,
                 NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date, NEW.status,
                 NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_model_update
            AFTER UPDATE ON M_MODEL
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_MODEL
                (ID_MODEL, NAMA_MODEL, KETERANGAN,
                 create_by, create_date, update_by, update_date, status,
                 attr1, attr2, attr3)
                VALUES
                (NEW.ID_MODEL, NEW.NAMA_MODEL, NEW.KETERANGAN,
                 NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date, 2,
                 NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // status otomatis di-update
        DB::unprepared('
            CREATE TRIGGER trg_status_model
            BEFORE UPDATE ON M_MODEL
            FOR EACH ROW
            BEGIN
                SET NEW.status = 2;
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_model_delete
            AFTER DELETE ON M_MODEL
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_MODEL
                (ID_MODEL, NAMA_MODEL, KETERANGAN,
                 create_by, create_date, update_by, update_date, status,
                 attr1, attr2, attr3)
                VALUES
                (OLD.ID_MODEL, OLD.NAMA_MODEL, OLD.KETERANGAN,
                 OLD.create_by, OLD.create_date, OLD.update_by, OLD.update_date, 99,
                 OLD.attr1, OLD.attr2, OLD.attr3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger dulu supaya tidak error saat rollback
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_model_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_model_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_model_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_model');
        
        Schema::dropIfExists('M_MODEL');
    }
};
