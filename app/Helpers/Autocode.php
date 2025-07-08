<?php

use Illuminate\Support\Facades\DB;

class Autocode
{
    public static function code($table, $field, $prefix)
    {
        $sql = "SELECT IFNULL(MAX($field), 'B-000') as $field FROM $table";
        $kodebarang = DB::select($sql);

        foreach ($kodebarang as $kdbrg) {
            $kd = $kdbrg->$field;
        }

        $noawal = substr($kd, -3);
        $noakhir = $noawal + 1;

        $noakhir = $prefix . '-' . str_pad($noakhir, 3, "0", STR_PAD_LEFT);

        return $noakhir;
    }
}
