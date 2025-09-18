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
        Schema::create('M_STATUS', function (Blueprint $table) {
            $table->integer('ID_STATUS', true);
            $table->string('nama_status', 20);
            $table->string('keterangan', 200)->nullable();
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('status')->default(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_status_insert
            AFTER INSERT ON M_STATUS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_STATUS
                (ID_STATUS, nama_status, keterangan, create_by, create_date, update_by, update_date, status,
                 param1, param2, param3)
                VALUES
                (NEW.ID_STATUS, NEW.nama_status, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by,
                 NEW.update_date, NEW.status, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_status_update
            AFTER UPDATE ON M_STATUS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_STATUS
                (ID_STATUS, nama_status, keterangan, create_by, create_date, update_by, update_date, status,
                 param1, param2, param3)
                VALUES
                (NEW.ID_STATUS, NEW.nama_status, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by,
                 NEW.update_date, 2, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger BEFORE UPDATE → set status = 2
        DB::unprepared('
            CREATE TRIGGER trg_update_status
            BEFORE UPDATE ON M_STATUS
            FOR EACH ROW
            BEGIN
                SET NEW.status = 2;
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_status_delete
            AFTER DELETE ON M_STATUS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_STATUS
                (ID_STATUS, nama_status, keterangan, create_by, create_date, update_by, update_date, status,
                 param1, param2, param3)
                VALUES
                (OLD.ID_STATUS, OLD.nama_status, OLD.keterangan, OLD.create_by, OLD.create_date, OLD.update_by,
                 OLD.update_date, 99, OLD.param1, OLD.param2, OLD.param3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger biar rollback aman
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_status_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_status_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_status_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_status');

        Schema::dropIfExists('M_STATUS');
    }
};
