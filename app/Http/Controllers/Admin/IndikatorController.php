<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Indikator;
use App\Models\Area;
use App\Models\SubArea;
use App\Models\OpsiJawaban;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class IndikatorController extends Controller
{
    public function index(Request $request)
    {
        $masterPeriodeId = Periode::where('tahun', 2025)->first()?->id;
        $indikators = Indikator::with('area', 'subArea')
            ->where('periode_id', $masterPeriodeId)
            ->when($request->area_id, fn($q) => $q->where('area_id', $request->area_id))
            ->when($request->sub_area_id, fn($q) => $q->where('sub_area_id', $request->sub_area_id))
            ->when($request->kategori, fn($q) => $q->where('kategori', $request->kategori))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.indikator.list', [
            'indikators' => $indikators,
            'areaList' => Area::all(),
            'subAreaList' => SubArea::all(),
            'kategoriList' => ['pemenuhan', 'reform'],
        ]);
    }

    public function create()
    {
        $areas = Area::with('subAreas')->get();
        $periodes = Periode::all();
        $masterPeriode = Periode::where('tahun', 2025)->first();
        return view('admin.indikator.create', compact('areas', 'periodes', 'masterPeriode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'nama_indikator' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'sub_area_id' => 'required|exists:sub_areas,id',
            'tipe_jawaban' => 'required|in:ya/tidak,abcde,esai',
            'kategori' => 'required|in:reform,pemenuhan',
        ]);

        $periodeId = $request->input('periode_id') ?? Periode::where('tahun', 2025)->first()?->id;

        $indikator = Indikator::create([
            'periode_id' => $periodeId,
            'kategori' => $request->kategori,
            'pertanyaan' => $request->pertanyaan,
            'nama_indikator' => $request->nama_indikator,
            'area_id' => $request->area_id,
            'sub_area_id' => $request->sub_area_id,
            'tipe_jawaban' => $request->tipe_jawaban,
            'bobot' => 0,
            'status' => 'draft',
        ]);

        if ($request->tipe_jawaban === 'ya/tidak') {
            OpsiJawaban::insert([
                ['indikator_id' => $indikator->id, 'opsi' => 'A', 'teks' => 'Ya', 'bobot' => 1.0],
                ['indikator_id' => $indikator->id, 'opsi' => 'B', 'teks' => 'Tidak', 'bobot' => 0.0]
            ]);
        }

        if ($request->tipe_jawaban === 'abcde' && $request->has('opsi_jawaban')) {
            foreach ($request->opsi_jawaban as $kode => $opsi) {
                if (!empty($opsi['teks']) && isset($opsi['bobot'])) {
                    OpsiJawaban::create([
                        'indikator_id' => $indikator->id,
                        'opsi' => $kode,
                        'teks' => $opsi['teks'],
                        'bobot' => $opsi['bobot'],
                    ]);
                }
            }
        }

        return redirect()->route('indikator.index')->with('success', 'Indikator berhasil ditambahkan sebagai data master');
    }

    public function edit($id)
    {
        $indikator = Indikator::with('opsiJawaban')->findOrFail($id);
        $areas = Area::with('subAreas')->get();
        $subAreas = SubArea::where('area_id', $indikator->area_id)->get();
        $tipeList = ['ya/tidak', 'abcde', 'esai'];
        $totalBobot = $indikator->tipe_jawaban === 'abcde' ? $indikator->opsiJawaban->sum('bobot') : 1.0;

        return view('admin.indikator.edit', compact('indikator', 'areas', 'subAreas', 'tipeList', 'totalBobot'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'nama_indikator' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'sub_area_id' => 'required|exists:sub_areas,id',
            'tipe_jawaban' => 'required|in:ya/tidak,abcde,esai',
        ]);

        $indikator = Indikator::findOrFail($id);
        $indikator->update($request->only('pertanyaan', 'nama_indikator', 'area_id', 'sub_area_id', 'tipe_jawaban'));

        if ($request->has('opsi_dihapus')) {
            OpsiJawaban::whereIn('id', $request->opsi_dihapus)->delete();
        }

        if ($request->has('opsi')) {
            foreach ($request->opsi as $data) {
                if (isset($data['id'])) {
                    OpsiJawaban::where('id', $data['id'])->update($data);
                } else {
                    $data['indikator_id'] = $indikator->id;
                    OpsiJawaban::create($data);
                }
            }
        }

        if ($request->filled('from') && $request->from === 'template') {
            return redirect()
                ->route('indikator.template', [
                    'periode_id' => $request->periode_id,
                    'kategori' => $request->kategori,
                ])
                ->with('success', 'Indikator berhasil diperbarui.');
        }

        return redirect()->route('indikator.index')->with('success', 'Data indikator berhasil diperbarui');
    }

    public function destroy($id)
    {
        Indikator::destroy($id);
        return back()->with('success', 'Data Indikator berhasil dihapus');
    }

 public function togglePublish($id)
{
    $indikator = Indikator::findOrFail($id);

    $indikator->status = $indikator->status === 'published' ? 'draft' : 'published';
    $indikator->save();

    if ($indikator->periode_id) {
        if ($indikator->status === 'published') {
            DB::table('indikator_periode')->updateOrInsert(
                ['indikator_id' => $indikator->id, 'periode_id' => $indikator->periode_id],
                ['published' => true, 'updated_at' => now()]
            );
        } else {
            DB::table('indikator_periode')
                ->where('indikator_id', $indikator->id)
                ->where('periode_id', $indikator->periode_id)
                ->delete();
        }
    }

    return back()->with('success', 'indikator berhasil dipublish.');
}



    // public function publish($id)
    // {
    //     $indikator = Indikator::findOrFail($id);

    //     if (!$indikator->periode_id) {
    //         return back()->with('error', 'Gagal publish: indikator ini belum memiliki periode_id.');
    //     }

    //     $indikator->status = 'published';
    //     $indikator->save();

    //     DB::table('indikator_periode')->updateOrInsert(
    //         ['indikator_id' => $id, 'periode_id' => $indikator->periode_id],
    //         ['published' => true, 'updated_at' => now()]
    //     );

    //     return back()->with('success', 'Indikator berhasil dipublish.');
    // }

    // public function unpublish($id)
    // {
    //     $indikator = Indikator::findOrFail($id);
    //     $indikator->status = 'draft';
    //     $indikator->save();

    //     DB::table('indikator_periode')
    //         ->where('indikator_id', $id)
    //         ->where('periode_id', $indikator->periode_id)
    //         ->delete();

    //     return back()->with('success', 'Indikator berhasil di-unpublish.');
    // }

    public function publishdata(Request $request)
    {
        $request->validate([
            'indikator_ids' => 'required|array',
            'indikator_ids.*' => 'exists:indikators,id',
        ]);

        foreach ($request->indikator_ids as $id) {
            $indikator = Indikator::find($id);
            if ($indikator && $indikator->periode_id) {
                $indikator->status = 'published';
                $indikator->save();

                DB::table('indikator_periode')->updateOrInsert(
                    ['indikator_id' => $id, 'periode_id' => $indikator->periode_id],
                    ['published' => true, 'updated_at' => now()]
                );
            }
        }

        return back()->with('success', 'Beberapa indikator berhasil dipublish.');
    }
} 