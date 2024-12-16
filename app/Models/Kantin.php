<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kantin extends Model
{
    use HasFactory;
    protected $table = 'kantin';
    protected $fillable = [
        'nama_kantin',
        'id_karyawan',
        'metode_pembayaran',
        'no_telp'
    ];

    public function menu(){
        return $this->hasMany(Menu::class, 'id_kantin');
    }

    //id_karyawan -> id user
    public function user(){
        return $this->belongsTo(User::class, 'id_karyawan');
    }

    public function pesanan(){
        return $this->hasMany(Pesanan::class, 'id_kantin');
    }
}
