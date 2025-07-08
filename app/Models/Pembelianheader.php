<?php
// pembelianheader
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelianheader extends Model
{
    use HasFactory;

    protected $table = 'pembelian_header';
    protected $fillable = [
        'no_pembelian',
        'tgl_pembelian',
        'keterangan',
        'status',
        'id_vendor',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
