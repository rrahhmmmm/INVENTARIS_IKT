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
        Schema::create('M_TERMINAL', function (Blueprint $table) {
            $table->integer('ID_TERMINAL', true);
            $table->string('KODE_TERMINAL', 20);
            $table->string('NAMA_TERMINAL', 20);
            $table->string('LOKASI', 100)->nullable();
            $table->string('CREATE_BY', 100);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 100)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('STATUS')->default(1);
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
        });

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_terminal_insert
            AFTER INSERT ON M_TERMINAL
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_TERMINAL
                (ID_TERMINAL, KODE_TERMINAL, NAMA_TERMINAL, LOKASI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, attr1, attr2, attr3)
                VALUES
                (NEW.ID_TERMINAL, NEW.KODE_TERMINAL, NEW.NAMA_TERMINAL, NEW.LOKASI, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, NEW.STATUS, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_terminal_update
            AFTER UPDATE ON M_TERMINAL
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_TERMINAL
                (ID_TERMINAL, KODE_TERMINAL, NAMA_TERMINAL, LOKASI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, attr1, attr2, attr3)
                VALUES
                (NEW.ID_TERMINAL, NEW.KODE_TERMINAL, NEW.NAMA_TERMINAL, NEW.LOKASI, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, 2, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        DB::unprepared('
            CREATE TRIGGER trg_update_m
            BEFORE UPDATE ON M_TERMINAL
            FOR EACH ROW
            BEGIN
                SET NEW.status = 2;
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_terminal_delete
            AFTER DELETE ON M_TERMINAL
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_TERMINAL
                (ID_TERMINAL, KODE_TERMINAL, NAMA_TERMINAL, LOKASI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, attr1, attr2, attr3)
                VALUES
                (OLD.ID_TERMINAL, OLD.KODE_TERMINAL, OLD.NAMA_TERMINAL, OLD.LOKASI, OLD.CREATE_BY, OLD.CREATE_DATE,
                 OLD.UPDATE_BY, OLD.UPDATE_DATE, 99, OLD.attr1, OLD.attr2, OLD.attr3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger biar rollback aman
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_terminal_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_terminal_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_terminal_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_m');

        Schema::dropIfExists('M_TERMINAL');
    }
};
