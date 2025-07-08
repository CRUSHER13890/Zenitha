<?php
// modal
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modal extends Model
{
    use HasFactory;
    
    protected $table = 'modal';
    protected $fillable = [
        'kode_modal',
        'tgl_modal',
        'keterangan',
        'nominal',
        'user_id_created',
        'user_id_updated',
    ];

    public function userCreated()
    {
        return $this->belongsTo(User::class, 'user_id_created');
    }

    public function userUpdated()
    {
        return $this->belongsTo(User::class, 'user_id_updated');
    }
}
