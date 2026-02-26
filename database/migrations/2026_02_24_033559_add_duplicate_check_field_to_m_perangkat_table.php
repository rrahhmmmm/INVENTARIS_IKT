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
        Schema::table('M_PERANGKAT', function (Blueprint $table) {
            $table->string('duplicate_check_field', 20)->nullable()->after('param16');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('M_PERANGKAT', function (Blueprint $table) {
            $table->dropColumn('duplicate_check_field');
        });
    }
};
