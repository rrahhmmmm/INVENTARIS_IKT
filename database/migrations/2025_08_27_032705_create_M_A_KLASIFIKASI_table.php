<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('M_KLASIFIKASI', function (Blueprint $table) {
            $table->integer('ID_KLASIFIKASI', true);
            $table->string('KODE_KLASIFIKASI', 100);
            $table->string('KATEGORI', 100);
            $table->string('DESKRIPSI',1000);
            $table->date('START_DATE')->nullable();
            $table->date('END_DATE')->nullable();
            $table->string('CREATE_BY', 50);
            $table->timestamp('CREATE_DATE')->useCurrent();
            $table->string('UPDATE_BY', 50)->nullable();
            $table->timestamp('UPDATE_DATE')->useCurrentOnUpdate()->nullable();
            $table->integer('STATUS')->default(1);
            $table->string('attr1')->nullable();
            $table->string('attr2')->nullable();
            $table->string('attr3')->nullable();
        });

         // Trigger INSERT
    DB::unprepared('
    CREATE TRIGGER trg_insert_klasifikasi
    AFTER INSERT ON M_KLASIFIKASI
    FOR EACH ROW
    BEGIN
        INSERT INTO H_M_KLASIFIKASI
        (ID_KLASIFIKASI, KODE_KLASIFIKASI, KATEGORI, DESKRIPSI,
         START_DATE, END_DATE, CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
         STATUS, attr1, attr2, attr3)
        VALUES
        (NEW.ID_KLASIFIKASI, NEW.KODE_KLASIFIKASI, NEW.KATEGORI, NEW.DESKRIPSI,
         NEW.START_DATE, NEW.END_DATE, NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
         NEW.STATUS, NEW.attr1, NEW.attr2, NEW.attr3);
    END
');

// Trigger UPDATE
DB::unprepared('
    CREATE TRIGGER trg_update_klasifikasi
    AFTER UPDATE ON M_KLASIFIKASI
    FOR EACH ROW
    BEGIN
        INSERT INTO H_M_KLASIFIKASI
        (ID_KLASIFIKASI, KODE_KLASIFIKASI, KATEGORI, DESKRIPSI,
         START_DATE, END_DATE, CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
         STATUS, attr1, attr2, attr3)
        VALUES
        (NEW.ID_KLASIFIKASI, NEW.KODE_KLASIFIKASI, NEW.KATEGORI, NEW.DESKRIPSI,
         NEW.START_DATE, NEW.END_DATE, NEW.CREATE_BY, NEW.CREATE_DATE, NEW.UPDATE_BY, NEW.UPDATE_DATE,
         2, NEW.attr1, NEW.attr2, NEW.attr3);
    END
');

// Trigger BEFORE UPDATE
DB::unprepared('
    CREATE TRIGGER trg_status_klasifikasi
    BEFORE UPDATE ON M_KLASIFIKASI
    FOR EACH ROW
    BEGIN
        SET NEW.STATUS = 2;
    END
');

// Trigger DELETE
DB::unprepared('
    CREATE TRIGGER trg_delete_klasifikasi
    AFTER DELETE ON M_KLASIFIKASI
    FOR EACH ROW
    BEGIN
        INSERT INTO H_M_KLASIFIKASI
        (ID_KLASIFIKASI, KODE_KLASIFIKASI, KATEGORI, DESKRIPSI,
         START_DATE, END_DATE, CREATE_BY, CREATE_DATE, UPDATE_BY, UPDATE_DATE,
         STATUS, attr1, attr2, attr3)
        VALUES
        (OLD.ID_KLASIFIKASI, OLD.KODE_KLASIFIKASI, OLD.KATEGORI, OLD.DESKRIPSI,
         OLD.START_DATE, OLD.END_DATE, OLD.CREATE_BY, OLD.CREATE_DATE, OLD.UPDATE_BY, OLD.UPDATE_DATE,
         99, OLD.attr1, OLD.attr2, OLD.attr3);
    END
');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_insert_klasifikasi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_klasifikas');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_klasifikasi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_delete_klasifikasi');
        Schema::dropIfExists('M_A_KLASIFIKASI');
    }
};
