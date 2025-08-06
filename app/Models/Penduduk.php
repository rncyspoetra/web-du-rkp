<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class);
    }

    protected $fillable = [
        'kartu_keluarga_id',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan',
        'jenis_pekerjaan',
        'status_perkawinan',
        'status_hubungan_dalam_keluarga',
        'kewarganegaraan',
        'nama_ayah',
        'nama_ibu',
    ];
}
