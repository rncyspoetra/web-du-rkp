<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DaftarUsulanRKP extends Model
{
    protected $table = 'usulan_rkp';

    protected $fillable = [
        'bidang',
        'jenis_kegiatan',
        'lokasi',
        'volume',
        'perkiraan_waktu_pelaksanaan',
        'prakiraan_biaya_jumlah',
        'sumber_pembiayaan_id',
    ];

    public function sumberPembiayaan(): BelongsTo
    {
        return $this->belongsTo(SumberPembiayaan::class);
    }
}
