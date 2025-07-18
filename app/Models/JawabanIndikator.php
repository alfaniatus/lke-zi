<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanIndikator extends Model
{
    protected $fillable = [
        'indikator_id', 'periode_id',   'user_id',  'jawaban', 'nilai', 'persen', 'catatan', 'bukti', 'link', 'is_submitted', 'status', 'status_validasi', 'catatan_admin', 'submit_ulang',
    ];
    public function indikator()
{
    return $this->belongsTo(\App\Models\Indikator::class, 'indikator_id');
}

} 