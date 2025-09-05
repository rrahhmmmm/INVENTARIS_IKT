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
        Schema::create('M_LOKASI', function (Blueprint $table) {
            $table->integer('ID_LOKASI', true);
            $table->string('NAMA_LOKASI', 20);
            $table->string('ALAMAT', 200)->nullable();
            $table->integer('ID_TERMINAL');
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('status')->defult(1);
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });
        DB::unprepared ('
        CREATE TRIGGER `trg_m_lokasi_delete` AFTER DELETE ON `M_LOKASI`
 FOR EACH ROW BEGIN 
    INSERT INTO H_M_LOKASI (
        ID_LOKASI, nama_lokasi, alamat, ID_terminal,
        create_by, create_date, update_by, update_date,
        status, param1, param2, param3
    )
    VALUES (
        OLD.ID_LOKASI, OLD.nama_lokasi, OLD.alamat, OLD.ID_terminal,
        OLD.create_by, OLD.create_date, OLD.update_by, OLD.update_date,
        99, OLD.param1, OLD.param2, OLD.param3
    );
END
');

    DB::unprepared ('
CREATE TRIGGER `trg_m_lokasi_insert` AFTER INSERT ON `M_LOKASI`
 FOR EACH ROW BEGIN
    INSERT INTO H_M_LOKASI (
        ID_LOKASI, nama_lokasi, alamat, ID_terminal,
        create_by, create_date, update_by, update_date,
        status, param1, param2, param3
    )
    VALUES (
        NEW.ID_LOKASI, NEW.nama_lokasi, NEW.alamat, NEW.ID_terminal,
        NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date,
        NEW.status, NEW.param1, NEW.param2, NEW.param3
    );
END
');

    DB::unprepared ('
    CREATE TRIGGER `trg_m_lokasi_update` AFTER UPDATE ON `M_LOKASI`
    FOR EACH ROW BEGIN
    INSERT INTO H_M_LOKASI (
        ID_LOKASI, nama_lokasi, alamat, ID_terminal,
        create_by, create_date, update_by, update_date,
        status, param1, param2, param3
    )
    VALUES (
        NEW.ID_LOKASI, NEW.nama_lokasi, NEW.alamat, NEW.ID_terminal,
        NEW.create_by, NEW.create_date, NEW.update_by, NEW.update_date,
        22, NEW.param1, NEW.param2, NEW.param3
    );
END
');

        DB::unprepared('
            CREATE TRIGGER trg_status_lokasi
            BEFORE UPDATE ON M_LOKASI
            FOR EACH ROW
            BEGIN
                SET NEW.status = 2;
            END
        ');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   //drop trigger
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_lokasi_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_lokasi_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_lokasi_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_status_lokasi');
       //drop table
        Schema::dropIfExists('M_LOKASI');
    }
};
