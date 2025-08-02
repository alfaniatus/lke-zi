<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Indikator extends Model
{
    use HasFactory;

    protected $fillable = ['pertanyaan', 'area_id', 'sub_area_id', 'kategori', 'nama_indikator', 'tipe_jawaban', 'bobot', 'status', 'periode_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function subArea()
    {
        return $this->belongsTo(SubArea::class);
    }

    public function opsiJawaban()
    {
        return $this->hasMany(OpsiJawaban::class);
    }

    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class);
    }

    public function indikatorPeriodes()
    {
        return $this->hasMany(IndikatorPeriode::class);
    }

    public function periodes()
    {
        return $this->belongsToMany(Periode::class, 'indikator_periode')->withPivot('published')->withTimestamps();
    }

    public function publishedForPeriode($periodeId)
    {
        return DB::table('indikator_periode')->where('indikator_id', $this->id)->where('periode_id', $periodeId)->value('published') === 1;
    }
    public function periodeAktif()
    {
        return $this->hasMany(\App\Models\IndikatorPeriode::class)->where('published', true);
    }
}
