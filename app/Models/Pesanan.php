<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;
    protected $table = 'pesanan';
    protected $fillable = [
        'id_user',
        'id_kantin',
        'total',
        'metode_pembayaran',
        'bukti_pembayaran',
        'diantar_diambil',
        'lok_pengantaran',
        'status',
    ];
    //relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kantin()
    {
        return $this->belongsTo(Kantin::class, 'id_kantin');
    }

    //relasi ke menu
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan');
    }
}
