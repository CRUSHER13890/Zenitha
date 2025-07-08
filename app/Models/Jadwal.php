<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';
    protected $fillable = [
        'kode_jadwal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
        'id_layanan',
        'user_id_created',
        'user_id_updated',
    ];
}
