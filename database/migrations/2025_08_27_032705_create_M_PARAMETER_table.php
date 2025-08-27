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
        Schema::create('M_PARAMETER', function (Blueprint $table) {
            $table->integer('ID_PARAMETER', true);
            $table->string('Nilai_parameter', 20);
            $table->string('keterangan', 200)->nullable();
            $table->string('create_by', 100);
            $table->timestamp('create_date')->useCurrent();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('update_date')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('status');
            $table->string('param1')->nullable();
            $table->string('param2')->nullable();
            $table->string('param3')->nullable();
        });

        // Trigger INSERT
        DB::unprepared('
            CREATE TRIGGER trg_m_parameter_insert
            AFTER INSERT ON M_PARAMETER
            FOR EACH ROW
            BEGIN
                INSERT INTO H_PARAMETER
                (ID_PARAMETER, Nilai_parameter, keterangan, create_by, create_date, update_by, update_date, status,
                 param1, param2, param3)
                VALUES
                (NEW.ID_PARAMETER, NEW.Nilai_parameter, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by,
                 NEW.update_date, NEW.status, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_m_parameter_update
            AFTER UPDATE ON M_PARAMETER
            FOR EACH ROW
            BEGIN
                INSERT INTO H_PARAMETER
                (ID_PARAMETER, Nilai_parameter, keterangan, create_by, create_date, update_by, update_date, status,
                 param1, param2, param3)
                VALUES
                (NEW.ID_PARAMETER, NEW.Nilai_parameter, NEW.keterangan, NEW.create_by, NEW.create_date, NEW.update_by,
                 NEW.update_date, NEW.status, NEW.param1, NEW.param2, NEW.param3);
            END
        ');

        // Trigger DELETE
        DB::unprepared('
            CREATE TRIGGER trg_m_parameter_delete
            AFTER DELETE ON M_PARAMETER
            FOR EACH ROW
            BEGIN
                INSERT INTO H_PARAMETER
                (ID_PARAMETER, Nilai_parameter, keterangan, create_by, create_date, update_by, update_date, status,
                 param1, param2, param3)
                VALUES
                (OLD.ID_PARAMETER, OLD.Nilai_parameter, OLD.keterangan, OLD.create_by, OLD.create_date, OLD.update_by,
                 OLD.update_date, OLD.status, OLD.param1, OLD.param2, OLD.param3);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger dulu biar rollback aman
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_parameter_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_parameter_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_m_parameter_delete');

        Schema::dropIfExists('M_PARAMETER');
    }
};
