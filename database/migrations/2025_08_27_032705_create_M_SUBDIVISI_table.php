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
        Schema::create('M_SUBDIVISI', function (Blueprint $table) {
            $table->integer('ID_SUBDIVISI', true);
            $table->integer('ID_DIVISI')->nullable();
            $table->string('NAMA_SUBDIVISI', 100);
            $table->string('CREATE_BY', 100);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 100)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->string('deskripsi', 200)->nullable();
            $table->boolean('STATUS')->default(true);
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
        });

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_subdivisi_insert
            AFTER INSERT ON M_SUBDIVISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_SUBDIVISI
                (ID_SUBDIVISI, ID_DIVISI, NAMA_SUBDIVISI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, deskripsi, STATUS, attr1, attr2, attr3)
                VALUES
                (NEW.ID_SUBDIVISI, NEW.ID_DIVISI, NEW.NAMA_SUBDIVISI, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, NEW.deskripsi, NEW.STATUS, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_subdivisi_update
            AFTER UPDATE ON M_SUBDIVISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_SUBDIVISI
                (ID_SUBDIVISI, ID_DIVISI, NAMA_SUBDIVISI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, deskripsi, STATUS, attr1, attr2, attr3)
                VALUES
                (NEW.ID_SUBDIVISI, NEW.ID_DIVISI, NEW.NAMA_SUBDIVISI, NEW.CREATE_BY, NEW.CREATE_DATE,
                 NEW.UPDATE_BY, NEW.UPDATE_DATE, NEW.deskripsi, NEW.STATUS, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_subdivisi_delete
            AFTER DELETE ON M_SUBDIVISI
            FOR EACH ROW
            BEGIN
                INSERT INTO H_SUBDIVISI
                (ID_SUBDIVISI, ID_DIVISI, NAMA_SUBDIVISI, CREATE_BY, CREATE_DATE,
                 UPDATE_BY, UPDATE_DATE, deskripsi, STATUS, attr1, attr2, attr3)
                VALUES
                (OLD.ID_SUBDIVISI, OLD.ID_DIVISI, OLD.NAMA_SUBDIVISI, OLD.CREATE_BY, OLD.CREATE_DATE,
                 OLD.UPDATE_BY, OLD.UPDATE_DATE, OLD.deskripsi, OLD.STATUS, OLD.attr1, OLD.attr2, OLD.attr3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger biar rollback aman
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_subdivisi_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_subdivisi_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_subdivisi_delete');

        Schema::dropIfExists('M_SUBDIVISI');
    }
};
