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
        Schema::create('M_DIVISI', function (Blueprint $table) {
            $table->integer('ID_DIVISI', true);
            $table->string('NAMA_DIVISI', 100);
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('STATUS')->nullable();
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
        });

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_divisi_insert
            AFTER INSERT ON M_DIVISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_DIVISI
                (ID_DIVISI, NAMA_DIVISI, CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE, deskripsi, STATUS,
                 attr1, attr2, attr3)
                VALUES
                (NEW.ID_DIVISI, NEW.NAMA_DIVISI, NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
                 NEW.deskripsi, NEW.STATUS, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_divisi_update
            AFTER UPDATE ON M_DIVISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_DIVISI
                (ID_DIVISI, NAMA_DIVISI, CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE, deskripsi, STATUS,
                 attr1, attr2, attr3)
                VALUES
                (NEW.ID_DIVISI, NEW.NAMA_DIVISI, NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
                 NEW.deskripsi, NEW.STATUS, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_divisi_delete
            AFTER DELETE ON M_DIVISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_DIVISI
                (ID_DIVISI, NAMA_DIVISI, CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE, deskripsi, STATUS,
                 attr1, attr2, attr3)
                VALUES
                (OLD.ID_DIVISI, OLD.NAMA_DIVISI, OLD.CREATE_BY, OLD.CREATE_DATE, OLD.UPDATE_BY, OLD.UPDATE_DATE,
                 OLD.deskripsi, OLD.STATUS, OLD.attr1, OLD.attr2, OLD.attr3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger dulu supaya rollback aman
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_divisi_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_divisi_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_divisi_delete');

        Schema::dropIfExists('M_DIVISI');
    }
};
