<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorPeriode;
use App\Models\Periode;
use Illuminate\Support\Facades\Auth;

class ProgressBarController extends Controller
{
    public function index($kategori)
{
    $periodeAktif = Periode::where('is_active', true)->first();

    if (!$periodeAktif) {
        return view("admin.sub-area.$kategori", [
            'kategori' => $kategori,
            'totalIndikator' => 0,
            'jawabanTerisi' => 0,
            'persentase' => 0,
            'progressWidth' => '0%',
        ]);
    }


    $indikatorPeriode = IndikatorPeriode::where('periode_id', $periodeAktif->id)
        ->whereHas('indikator', function ($query) use ($kategori) {
            $query->where('kategori', $kategori);
        })
        ->with(['indikator.jawabanIndikator']) 
        ->get();

    $totalIndikator = $indikatorPeriode->count();


    $jawabanTerisi = $indikatorPeriode->filter(function ($item) {
        return optional($item->indikator)->jawabanIndikator->isNotEmpty();
    })->count();

    $persentase = $totalIndikator > 0 ? round(($jawabanTerisi / $totalIndikator) * 100, 2) : 0;
    $progressWidth = $persentase . '%';

    return view("admin.sub-area.$kategori", [
        'kategori' => $kategori,
        'totalIndikator' => $totalIndikator,
        'jawabanTerisi' => $jawabanTerisi,
        'persentase' => $persentase,
        'progressWidth' => $progressWidth,
    ]);
}

}
