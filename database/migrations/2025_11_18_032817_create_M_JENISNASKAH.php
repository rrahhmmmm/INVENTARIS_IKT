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
        Schema::create('M_JENISNASKAH', function (Blueprint $table) {
            $table->integer('ID_JENISNASKAH', true);
            $table->string('NAMA_JENIS', 50);
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->default(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });

        // TRIGGER INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_jenisnaskah_insert
            AFTER INSERT ON M_JENISNASKAH
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_JENISNASKAH
                (ID_JENISNASKAH, NAMA_JENIS, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_JENISNASKAH, NEW.NAMA_JENIS, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, NEW.STATUS, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // TRIGGER BEFORE UPDATE → ubah status otomatis
        DB::unprepared('
            CREATE TRIGGER trg_status_jenisnaskah
            BEFORE UPDATE ON M_JENISNASKAH
            FOR EACH ROW
            BEGIN
                SET NEW.STATUS = 2;
            END
        ');

        // TRIGGER UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_jenisnaskah_update
            AFTER UPDATE ON M_JENISNASKAH
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_JENISNASKAH
                (ID_JENISNASKAH, NAMA_JENIS, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_JENISNASKAH, NEW.NAMA_JENIS, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, 2, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // TRIGGER DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_jenisnaskah_delete
            AFTER DELETE ON M_JENISNASKAH
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_JENISNASKAH
                (ID_JENISNASKAH, NAMA_JENIS, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, STATUS, param1, param2, param3)
                VALUES
                (OLD.ID_JENISNASKAH, OLD.NAMA_JENIS, OLD.CREATE_BY, OLD.CREATE_DATE,
                 OLD.UPDATE_BY, OLD.UPDATE_DATE, 99, OLD.param1, OLD.param2, OLD.param3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_jenisnaskah_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_jenisnaskah_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_jenisnaskah_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_jenisnaskah');

        Schema::dropIfExists('M_JENISNASKAH');
    }
};
