<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE VIEW `v_waktu`  AS
                SELECT concat(date_format(current_timestamp(),'%Y'),'-01') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-02') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-03') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-04') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-05') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-06') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-07') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-08') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-09') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-10') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-11') AS `waktu`
                union select concat(date_format(current_timestamp(),'%Y'),'-12') AS `waktu`;
                "
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_waktu;');
    }
};
