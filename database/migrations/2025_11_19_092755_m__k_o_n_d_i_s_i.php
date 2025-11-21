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
        // --- TABEL UTAMA ---
        Schema::create('M_KONDISI', function (Blueprint $table) {
            $table->integer('ID_KONDISI', true);
            $table->string('NAMA_KONDISI');
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->default(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | TRIGGERS
        |--------------------------------------------------------------------------
        */

        // TRIGGER INSERT → simpan ke history
        DB::unprepared('
            CREATE TRIGGER trg_kondisi_insert
            AFTER INSERT ON M_KONDISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_KONDISI
                (ID_KONDISI, NAMA_KONDISI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_KONDISI, NEW.NAMA_KONDISI, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, NEW.STATUS, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // TRIGGER BEFORE UPDATE → otomatis mengubah status = 2
        DB::unprepared('
            CREATE TRIGGER trg_status_kondisi
            BEFORE UPDATE ON M_KONDISI
            FOR EACH ROW
            BEGIN
                SET NEW.STATUS = 2;
            END
        ');

        // TRIGGER AFTER UPDATE → simpan perubahan ke history
        DB::unprepared('
            CREATE TRIGGER trg_kondisi_update
            AFTER UPDATE ON M_KONDISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_KONDISI
                (ID_KONDISI, NAMA_KONDISI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_KONDISI, NEW.NAMA_KONDISI, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, 2, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // TRIGGER DELETE → simpan riwayat dengan STATUS = 99 (deleted)
        DB::unprepared('
            CREATE TRIGGER trg_kondisi_delete
            AFTER DELETE ON M_KONDISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_KONDISI
                (ID_KONDISI, NAMA_KONDISI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, param1, param2, param3)
                VALUES
                (OLD.ID_KONDISI, OLD.NAMA_KONDISI, OLD.CREATE_BY, OLD.CREATE_DATE,
                 OLD.UPDATE_BY, OLD.UPDATE_DATE, 99, OLD.param1, OLD.param2, OLD.param3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_kondisi_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_kondisi_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_kondisi_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_kondisi');

        Schema::dropIfExists('M_KONDISI');
    }
};
