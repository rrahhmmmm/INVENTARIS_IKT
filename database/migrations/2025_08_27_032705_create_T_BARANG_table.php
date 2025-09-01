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
        Schema::create('T_BARANG', function (Blueprint $table) {
            $table->integer('ID_BARANG', true);
            $table->string('KODE_BARANG', 20);
            $table->string('NAMA_BARANG', 100);
            $table->integer('ID_TIPE');
            $table->integer('ID_STATUS');
            $table->integer('ID_LOKASI');
            $table->integer('ID_ANGGARAN');
            $table->integer('ID_PARAMETER');
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
            CREATE TRIGGER trg_t_barang_insert
            AFTER INSERT ON T_BARANG
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_BARANG
                (ID_BARANG, KODE_BARANG, NAMA_BARANG, ID_TIPE, ID_STATUS, ID_LOKASI, ID_ANGGARAN, ID_PARAMETER,
                 create_by, create_date, update_by, update_date, status, attr1, attr2, attr3)
                VALUES
                (NEW.ID_BARANG, NEW.KODE_BARANG, NEW.NAMA_BARANG, NEW.ID_TIPE, NEW.ID_STATUS, NEW.ID_LOKASI,
                 NEW.ID_ANGGARAN, NEW.ID_PARAMETER, NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date,
                 NEW.status, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_t_barang_update
            AFTER UPDATE ON T_BARANG
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_BARANG
                (ID_BARANG, KODE_BARANG, NAMA_BARANG, ID_TIPE, ID_STATUS, ID_LOKASI, ID_ANGGARAN, ID_PARAMETER,
                 create_by, create_date, update_by, update_date, status, attr1, attr2, attr3)
                VALUES
                (NEW.ID_BARANG, NEW.KODE_BARANG, NEW.NAMA_BARANG, NEW.ID_TIPE, NEW.ID_STATUS, NEW.ID_LOKASI,
                 NEW.ID_ANGGARAN, NEW.ID_PARAMETER, NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date,
                 NEW.status, NEW.attr1, NEW.attr2, NEW.attr3);
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_t_barang_delete
            AFTER DELETE ON T_BARANG
            FOR EACH ROW
            BEGIN
                INSERT INTO H_M_BARANG
                (ID_BARANG, KODE_BARANG, NAMA_BARANG, ID_TIPE, ID_STATUS, ID_LOKASI, ID_ANGGARAN, ID_PARAMETER,
                 create_by, create_date, update_by, update_date, status, attr1, attr2, attr3)
                VALUES
                (OLD.ID_BARANG, OLD.KODE_BARANG, OLD.NAMA_BARANG, OLD.ID_TIPE, OLD.ID_STATUS, OLD.ID_LOKASI,
                 OLD.ID_ANGGARAN, OLD.ID_PARAMETER, OLD.create_by, OLD.create_date, OLD.update_by, OLD.update_date,
                 OLD.status, OLD.attr1, OLD.attr2, OLD.attr3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t_barang_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t_barang_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_t_barang_delete');

        Schema::dropIfExists('T_BARANG');
    }
};
