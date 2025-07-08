<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';
    protected $fillable = [
        'kode_layanan',
        'nama_layanan',
        'harga_layanan',
        'deskripsi',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
