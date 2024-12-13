<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = [
        'nama_menu',
        'deskripsi',
        'harga',
        'image',
        'stok',
        'id_kantin'
    ];

    public function kantin(){
        return $this->belongsTo(Kantin::class, 'id_kantin');
    }

    public function pesananDetail()
{
    return $this->hasMany(DetailPesanan::class, 'id_menu');
}
}
