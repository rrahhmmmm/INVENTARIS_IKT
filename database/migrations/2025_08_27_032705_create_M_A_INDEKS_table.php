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
        Schema::create('M_INDEKS', function (Blueprint $table) {
            $table->integer('ID_INDEKS', true);
            $table->string('NO_INDEKS', 100);
            $table->string('WILAYAH', 100);
            $table->string('NAMA_INDEKS', 1000);
            $table->date('START_DATE')->nullable();
            $table->date('END_DATE')->nullable();
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
            CREATE TRIGGER trg_m_indeks_insert
            AFTER INSERT ON M_INDEKS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_INDEKS
                (ID_INDEKS, NO_INDEKS, WILAYAH, NAMA_INDEKS, START_DATE, END_DATE,
                 CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
                 STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_INDEKS, NEW.NO_INDEKS, NEW.WILAYAH, NEW.NAMA_INDEKS, NEW.START_DATE, NEW.END_DATE,
                 NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
                 NEW.STATUS, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_indeks_update
            AFTER UPDATE ON M_INDEKS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_INDEKS
                (ID_INDEKS, NO_INDEKS, WILAYAH, NAMA_INDEKS, START_DATE, END_DATE,
                 CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
                 STATUS, param1, param2, param3)
                VALUES
                (NEW.ID_INDEKS, NEW.NO_INDEKS, NEW.WILAYAH, NEW.NAMA_INDEKS, NEW.START_DATE, NEW.END_DATE,
                 NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
                 NEW.STATUS, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger BEFORE UPDATE (ubah status jadi 2 otomatis)
        DB::unprepared('
            CREATE TRIGGER trg_status_indeks
            BEFORE UPDATE ON M_INDEKS
            FOR EACH ROW
            BEGIN
                SET NEW.STATUS = 2;
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_indeks_delete
            AFTER DELETE ON M_INDEKS
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_INDEKS
                (ID_INDEKS, NO_INDEKS, WILAYAH, NAMA_INDEKS, START_DATE, END_DATE,
                 CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
                 STATUS, param1, param2, param3)
                VALUES
                (OLD.ID_INDEKS, OLD.NO_INDEKS, OLD.WILAYAH, OLD.NAMA_INDEKS, OLD.START_DATE, OLD.END_DATE,
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
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_indeks_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_indeks_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_indeks');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_indeks_delete');
        Schema::dropIfExists('M_INDEKS');
    }
};
