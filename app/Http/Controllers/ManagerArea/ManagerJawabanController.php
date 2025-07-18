<?php

namespace App\Http\Controllers\ManagerArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JawabanIndikator;
use App\Models\Periode;
use App\Models\Indikator;
use App\Models\IndikatorPeriode;

class ManagerJawabanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'jawaban' => 'required|array',
                'bukti' => 'required|array',
                'link' => 'required|array',
                'nilai' => 'nullable|array',
                'persen' => 'nullable|array',
                'catatan' => 'nullable|array',
            ],
            [
                'bukti.required' => 'Setiap indikator wajib diisi kolom Bukti.',
                'link.required' => 'Setiap indikator wajib diisi kolom Link.',
            ],
        );

        $areaId = $request->input('area_id');
        $kategori = $request->input('kategori');
        $periodeId = $request->input('periode_id');
        $periode = Periode::findOrFail($periodeId);
        $userId = auth()->id();

        foreach ($validated['jawaban'] as $indikatorId => $jawab) {
            JawabanIndikator::updateOrCreate(
                [
                    'indikator_id' => $indikatorId,
                    'periode_id' => $periode->id,
                    'user_id' => $userId,
                ],
                [
                    'jawaban' => $jawab,
                    'nilai' => $validated['nilai'][$indikatorId] ?? null,
                    'persen' => $validated['persen'][$indikatorId] ?? null,
                    'catatan' => $validated['catatan'][$indikatorId] ?? null,
                    'bukti' => $validated['bukti'][$indikatorId],
                    'link' => $validated['link'][$indikatorId],
                    'user_id' => $userId,
                    'is_submitted' => false,
                    'status_validasi' => null,
                    'catatan_admin' => null,
                ],
            );
        }

        return redirect()->route('manager-area.jawaban.preview', [
            'area' => $areaId,
            'kategori' => $kategori,
            'periode_id' => $periode->id,
        ]);
    }

    public function submit(Request $request, $area, $kategori)
    {
        $periodeId = $request->input('periode_id');
        if (!$periodeId) {
            return back()->with('error', 'Periode tidak ditemukan.');
        }

        $userId = auth()->id();

        JawabanIndikator::where('periode_id', $periodeId)
            ->where('user_id', $userId)
            ->whereHas('indikator', function ($query) use ($area, $kategori) {
                $query->where('area_id', $area)->where('kategori', $kategori);
            })
            ->update([
                'status' => 'submitted',
                'is_submitted' => true,
                'status_validasi' => 'pending', 
                'catatan_admin' => null, 
            ]);
        return redirect()
            ->route('manager-area.jawaban.preview', [
                'area' => $area,
                'kategori' => $kategori,
                'periode_id' => $periodeId,
            ])
            ->with('success', 'Jawaban berhasil dikirim untuk divalidasi.');
    }

    public function preview(Request $request, $area, $kategori)
    {
        $periodeId = $request->input('periode_id') ?? $request->old('periode_id');
        if (!$periodeId) {
            return back()->with('error', 'Periode tidak ditemukan.');
        }

        $periode = Periode::findOrFail($periodeId);
        $userId = auth()->id();

        $jawabans = JawabanIndikator::with('indikator')
            ->where('periode_id', $periode->id)
            ->where('user_id', $userId)
            ->whereHas('indikator', function ($query) use ($area, $kategori) {
                $query->where('area_id', $area)->where('kategori', $kategori);
            })
            ->get();

        $sudahDikirim = $jawabans->isNotEmpty() && $jawabans->every(fn($j) => $j->is_submitted);

        return view('manager-area.jawaban.preview', [
            'jawabans' => $jawabans,
            'periode' => $periode,
            'areaId' => $area,
            'currentKategori' => $kategori,
            'sudahDikirim' => $sudahDikirim,
        ]);
    }

    public function kembaliIsi(Request $request, $area, $kategori)
    {
        $periodeId = $request->input('periode_id');
        $periode = $periodeId ? Periode::findOrFail($periodeId) : Periode::orderByDesc('tahun')->first();

        $userId = auth()->id();

        $indikators = Indikator::with('opsiJawaban')
            ->where('area_id', $area)
            ->where('kategori', $kategori)
            ->whereHas('indikatorPeriodes', function ($q) use ($periode) {
                $q->where('periode_id', $periode->id)->where('published', true);
            })
            ->get();

        $totalIndikator = $indikators->count();

        $jawabanUser = JawabanIndikator::where('periode_id', $periode->id)
            ->where('user_id', $userId)
            ->whereHas('indikator', function ($query) use ($area, $kategori) {
                $query->where('area_id', $area)->where('kategori', $kategori);
            })
            ->get();

        $jumlahJawaban = $jawabanUser->count();
        $semuaSudahSubmit = $jawabanUser->isNotEmpty() && $jawabanUser->every(fn($j) => $j->is_submitted);

        if ($jumlahJawaban == $totalIndikator && $semuaSudahSubmit) {
            return redirect()
                ->route('manager-area.jawaban.preview', [
                    'area' => $area,
                    'kategori' => $kategori,
                    'periode_id' => $periode->id,
                ])
                ->with('info', 'Jawaban sudah lengkap dan dikirim. Anda tidak bisa mengubahnya.');
        }

        $jawabanSebelumnya = $jawabanUser->mapWithKeys(function ($item) {
            return [
                $item->indikator_id => [
                    'jawaban' => $item->jawaban,
                    'nilai' => $item->nilai,
                    'persen' => $item->persen,
                    'catatan' => $item->catatan,
                    'bukti' => $item->bukti,
                    'link' => $item->link,
                    'is_submitted' => $item->is_submitted,
                    'is_disabled' => $item->is_submitted ? 'disabled' : '',
                ],
            ];
        });

        return view('manager-area.indikator.index', [
            'indikators' => $indikators,
            'periode' => $periode,
            'kategori' => $kategori,
            'area' => $area,
            'areaId' => $area,
            'isianJawaban' => $jawabanSebelumnya,
            'semuaSudahSubmit' => $semuaSudahSubmit,
        ]);
    }
}
