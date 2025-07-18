<?php

namespace App\Http\Controllers\ManagerArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JawabanIndikator;

class ManagerHasilController extends Controller
{
    public function index()
    {
        $areaId = auth()->user()->area_id;

        $jawabans = JawabanIndikator::with('indikator.opsiJawaban')
            ->whereHas('indikator', fn($q) => $q->where('area_id', $areaId))
            ->get();

        return view('manager-area.hasil.index', [
    'jawabans' => $jawabans,
    'currentKategori' => 'hasil',
]);

        
    }

    public function edit($id)
{
    $jawaban = JawabanIndikator::with('indikator.opsiJawaban')->findOrFail($id);

    if (auth()->user()->area_id != $jawaban->indikator->area_id) {
        abort(403, 'Akses ditolak.');
    }

    if ($jawaban->status_validasi !== 'ditolak') {
        return redirect()->route('manager-area.hasil.index')
            ->with('error', 'Jawaban ini tidak perlu diedit.');
    }


    return view('manager-area.hasil.edit', [
        'jawaban' => $jawaban,
        'currentKategori' => 'hasil',
    ]);
}


public function update(Request $request, $id)
{
    $request->validate([
        'jawaban' => 'string',
        'catatan' => 'nullable|string|max:1000',
        'bukti'   => 'nullable|string|max:255',
        'link'    => 'nullable|string|max:255',
    ]);

    $jawaban = JawabanIndikator::findOrFail($id);

    if (auth()->user()->area_id != $jawaban->indikator->area_id) {
        abort(403, 'Akses ditolak.');
    }


    $jawaban->catatan         = $request->catatan;
    $jawaban->bukti           = $request->bukti;
    $jawaban->link            = $request->link;
    $jawaban->status_validasi = 'pending';
    $jawaban->submit_ulang    = true;

    $jawaban->save();

    return redirect()->route('manager-area.hasil.index')
        ->with('success', 'Perbaikan berhasil disimpan. Silakan kirim ulang ke admin.');
}


    public function submitUlang($id)
    {
        $jawaban = JawabanIndikator::findOrFail($id);

        if (auth()->user()->area_id != $jawaban->indikator->area_id) {
            abort(403);
        }

        if ($jawaban->status_validasi === 'pending' && $jawaban->submit_ulang) {
            $jawaban->submit_ulang = false;
            $jawaban->save();

            return redirect()->route('manager-area.hasil.index')
                ->with('success', 'Jawaban berhasil dikirim ulang ke admin.');
        }

        return redirect()->route('manager-area.hasil.index')
            ->with('error', 'Jawaban tidak bisa dikirim ulang.');
    }
}
