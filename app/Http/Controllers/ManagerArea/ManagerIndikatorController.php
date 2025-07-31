<?php

namespace App\Http\Controllers\ManagerArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Indikator;
use App\Models\Jawaban;
use App\Models\Periode;
use App\Models\IndikatorPeriode;
use App\Models\JawabanIndikator;

class ManagerIndikatorController extends Controller
{
    public function index(Request $request, $area, $kategori)
    {
        $periodeTerpilihId = $request->get('periode_id');

        $periodeQuery = \App\Models\Periode::whereHas('indikators', function ($query) use ($area, $kategori) {
            $query
                ->where('area_id', $area)
                ->where('kategori', $kategori)
                ->whereHas('indikatorPeriodes', function ($q) {
                    $q->where('published', true);
                });
        });

        if ($periodeTerpilihId) {
            $periode = $periodeQuery->where('id', $periodeTerpilihId)->first();
        } else {
            $periode = $periodeQuery->orderBy('tahun', 'asc')->first();
        }

        $periodeTerbaru = Periode::orderBy('tahun', 'desc')->first();

        $semuaPeriode = \App\Models\Periode::orderBy('tahun')->get();

        if (!$periode) {
            return view('manager-area.indikator.index', [
                'indikators' => [],
                'periode' => null,
                'kategori' => $kategori,
                'area' => $area,
                'areaId' => $area,
                'isianJawaban' => [],
                'semuaSudahSubmit' => false,
                'semuaPeriode' => $semuaPeriode,
                'currentKategori' => $kategori,
                'readonly' => true,
            ])->with('info', 'Belum ada indikator yang dipublish.');
        }

        $indikators = Indikator::with('opsiJawaban')
            ->where('area_id', $area)
            ->where('kategori', $kategori)
            ->whereHas('indikatorPeriodes', function ($q) use ($periode) {
                $q->where('periode_id', $periode->id)->where('published', true);
            })
            ->get();

        $userId = auth()->id();

        $jawabans = JawabanIndikator::where('periode_id', $periode->id)
            ->where('user_id', $userId)
            ->whereHas('indikator', function ($query) use ($area, $kategori) {
                $query->where('area_id', $area)->where('kategori', $kategori);
            })
            ->get();

        $totalIndikator = $indikators->count();
        $jumlahDiterima = $jawabans->where('status_validasi', 'diterima')->count();

        $readonly = $indikators->isEmpty() || $periode->id !== optional($periodeTerbaru)->id || ($totalIndikator > 0 && $jumlahDiterima === $totalIndikator);

        return view('manager-area.indikator.index', [
            'indikators' => $indikators,
            'periode' => $periode,
            'kategori' => $kategori,
            'area' => $area,
            'areaId' => $area,
            'isianJawaban' => [],
            'semuaSudahSubmit' => false,
            'semuaPeriode' => $semuaPeriode,
            'currentKategori' => $kategori,
            'readonly' => $readonly,
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
