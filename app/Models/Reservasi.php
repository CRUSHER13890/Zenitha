<?php
// resevasi m
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasi';
    protected $fillable = [
        'no_reservasi',
        'id_layanan',
        'ruangan',
        'tgl_reservasi',
        'status',
        'id_pelanggan',
        'waktu_mulai',
        'waktu_selesai',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
