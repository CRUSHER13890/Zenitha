<?php
// barang vendor
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangVendor extends Model
{
    use HasFactory;

    protected $table = 'barang_vendor';
    
    protected $fillable = [
        'barang_id',
        'vendor_id',
        'harga_satuan',
        'harga_jual',
        'persentase_keuntungan',
        'is_active',
        'user_id_created',
        'user_id_updated',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'persentase_keuntungan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'user_id_created');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'user_id_updated');
    }
}