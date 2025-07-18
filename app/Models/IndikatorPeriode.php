<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class IndikatorPeriode extends Model
{
    protected $table = 'indikator_periode';

    protected $fillable = [
        'indikator_id',
        'periode_id',
        'published',
    ];

    public $timestamps = true;

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}