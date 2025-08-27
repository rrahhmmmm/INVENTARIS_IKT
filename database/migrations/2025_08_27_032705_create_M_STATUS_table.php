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
            $table->boolean('status');
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
        });

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_status_insert
            AFTER INSERT ON M_STATUS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_STATUS
                (ID_STATUS, nama_status, keterangan, create_by, create_date, update_by, update_date, status,
                 attr1, attr2, attr3)
                VALUES
                (NEW.ID_STATUS, NEW.nama_status, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by,
                 NEW.update_date, NEW.status, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_status_update
            AFTER UPDATE ON M_STATUS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_STATUS
                (ID_STATUS, nama_status, keterangan, create_by, create_date, update_by, update_date, status,
                 attr1, attr2, attr3)
                VALUES
                (NEW.ID_STATUS, NEW.nama_status, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by,
                 NEW.update_date, NEW.status, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_status_delete
            AFTER DELETE ON M_STATUS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_STATUS
                (ID_STATUS, nama_status, keterangan, create_by, create_date, update_by, update_date, status,
                 attr1, attr2, attr3)
                VALUES
                (OLD.ID_STATUS, OLD.nama_status, OLD.keterangan, OLD.create_by, OLD.create_date, OLD.update_by,
                 OLD.update_date, OLD.status, OLD.attr1, OLD.attr2, OLD.attr3);
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

        Schema::dropIfExists('M_STATUS');
    }
};
