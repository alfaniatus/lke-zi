<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JawabanIndikator;
use App\Models\Periode;
use App\Models\Area;

class ValidasiJawabanController extends Controller
{
    public function index(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $areaId = $request->input('area_id');
        $kategori = $request->input('kategori');
        $statusValidasi = $request->input('status_validasi');

        $jawabans = JawabanIndikator::with([
                'indikator.opsiJawaban',
                'indikator.area',
                'indikator.subArea'
            ])
            ->where('is_submitted', true)
            ->when($statusValidasi === 'pending', function ($query) {
                $query->where('status_validasi', 'pending');
            })
            ->when($statusValidasi === 'ditolak', function ($query) {
                $query->where('status_validasi', 'ditolak');
            })
            ->when($statusValidasi === 'diterima', function ($query) {
                $query->where('status_validasi', 'diterima');
            })
            // Default: tampilkan jawaban yang belum validasi (pending) atau ditolak
            ->when(!$statusValidasi, function ($query) {
                $query->whereIn('status_validasi', ['pending', 'ditolak']);
            })
            ->when($periodeId, function ($query) use ($periodeId) {
                $query->whereHas('indikator.periodes', function ($periodeQuery) use ($periodeId) {
                    $periodeQuery->where('periodes.id', $periodeId);
                });
            })
            ->when($areaId, function ($query) use ($areaId) {
                $query->whereHas('indikator', function ($q) use ($areaId) {
                    $q->where('area_id', $areaId);
                });
            })
            ->when($kategori, function ($query) use ($kategori) {
                $query->whereHas('indikator', function ($q) use ($kategori) {
                    $q->where('kategori', $kategori);
                });
            })
            ->get();

        $periodes = Periode::orderByDesc('tahun')->get();
        $areas = Area::all();
        $kategoris = ['pemenuhan', 'reform'];
        $statusOptions = ['pending', 'diterima', 'ditolak'];

        return view('admin.validasi.index', compact(
            'jawabans', 'periodes', 'areas', 'kategoris', 'statusOptions'
        ));
    }

   public function simpan(Request $request)
{
    $data = $request->input('validasi', []);
    $errors = [];

    foreach ($data as $jawabanId => $item) {
        $jawaban = JawabanIndikator::find($jawabanId);
        if ($jawaban) {
            $status = $item['status'] ?? null;
            $catatan = $item['catatan'] ?? null;

            if ($status === 'ditolak' && (!preg_match('/[a-zA-Z]{5,}/', $catatan))) {
                $errors[] = "Catatan untuk jawaban ID $jawabanId harus berisi minimal 5 huruf alfabet.";
                continue;
            }

            $jawaban->status_validasi = $status;
            $jawaban->catatan_admin = $catatan;
            $jawaban->save();
        }
    }

    if (!empty($errors)) {
        return redirect()->back()->withErrors($errors)->withInput();
    }

    return redirect()->route('admin.validasi.index')
        ->with('success', 'Semua data berhasil divalidasi.');
}


}
