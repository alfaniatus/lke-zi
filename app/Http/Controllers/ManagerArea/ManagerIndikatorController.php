<?php

namespace App\Http\Controllers\ManagerArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Indikator;
use App\Models\Jawaban;
use App\Models\Periode;
use App\Models\IndikatorPeriode;


class ManagerIndikatorController extends Controller
{
public function index(Request $request, $area, $kategori)
{
  
    $periode = \App\Models\Periode::whereHas('indikators', function ($query) use ($area, $kategori) {
        $query->where('area_id', $area)
              ->where('kategori', $kategori)
              ->whereHas('indikatorPeriodes', function ($q) {
                  $q->where('published', true);
              });
    })
    ->orderBy('tahun', 'asc')
    ->first();

    if (!$periode) {
        return view('manager-area.indikator.index', [
            'indikators' => [],
            'periode' => null,
            'kategori' => $kategori,
            'area' => $area,
            'areaId' => $area,
            'isianJawaban' => [],
            'semuaSudahSubmit' => false,
            'currentKategori' => $kategori,
        ])->with('info', 'Belum ada indikator yang dipublish.');
    }

    $indikators = Indikator::with('opsiJawaban')
        ->where('area_id', $area)
        ->where('kategori', $kategori)
        ->whereHas('indikatorPeriodes', function ($q) use ($periode) {
            $q->where('periode_id', $periode->id)->where('published', true);
        })
        ->get();

    return view('manager-area.indikator.index', [
        'indikators' => $indikators,
        'periode' => $periode,
        'kategori' => $kategori,
        'area' => $area,
        'areaId' => $area,
        'isianJawaban' => [],
        'semuaSudahSubmit' => false,
        'currentKategori' => $kategori,
    ]);
}

    public function togglePublish($id)
    {
        $indikator = Indikator::findOrFail($id);

        if (!$indikator->periode_id) {
            $periode = Periode::latest()->first();
            $indikator->periode_id = $periode->id;
        }

        $indikator->is_published = !$indikator->is_published;
        $indikator->save();

        return redirect()->back()->with('success', 'Status publish indikator diperbarui.');
    }
}
