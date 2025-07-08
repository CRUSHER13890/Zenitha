<?php
// pembeliandetail
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembeliandetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_detail';
    protected $fillable = [
        'kuantitas',
        'harga_satuan',
        'id_barang',
        'id_pembelian_header',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
