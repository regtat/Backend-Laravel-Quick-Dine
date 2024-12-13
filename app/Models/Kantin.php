<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kantin extends Model
{
    protected $table = 'kantin';
    protected $fillable = [
        'nama_kantin',
        'id_karyawan',
    ];

    public function menu(){
        return $this->hasMany(Menu::class, 'id_kantin');
    }

    //id_karyawan -> id user
    public function karyawan(){
        return $this->belongsTo(User::class, 'id_karyawan');
    }

    public function pesanan(){
        return $this->hasMany(Pesanan::class, 'id_kantin');
    }
}
