<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberPembiayaan extends Model
{
    protected $fillable = [
        'sumber_pembiayaan',
        'tahun',
        'total_anggaran',
    ];

    public function usulanRkp()
    {
        return $this->hasMany(DaftarUsulanRKP::class, 'sumber_pembiayaan_id');
    }
}
