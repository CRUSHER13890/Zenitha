<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $fillable = [
        'kode_pelanggan',
        'nama_pelanggan',
        'no_hp',
        'tgl_lahir',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
