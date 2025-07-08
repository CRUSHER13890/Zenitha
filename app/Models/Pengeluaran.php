<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';
    protected $fillable = [
        'no_pengeluaran',
        'tgl_pengeluaran',
        'nominal',
        'tipe_pengeluaran',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
