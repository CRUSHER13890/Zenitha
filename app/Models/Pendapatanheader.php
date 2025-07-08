<?php
// pendapatan_h m
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendapatanheader extends Model
{
    use HasFactory;

    protected $table = 'pendapatan_header';
    protected $fillable = [
        'no_pendapatan',
        'tgl_pendapatan',
        'id_pelanggan',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
