<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Indikator;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    public function template(Request $request)
    {
        $periodes = Periode::orderBy('tahun', 'desc')->get();
        $indikators = collect();
        $periode = null;
        $kategori = $request->kategori ?? '';

        if ($request->filled('periode_id')) {
            $request->validate([
                'periode_id' => 'exists:periodes,id',
                'kategori' => 'nullable|in:reform,pemenuhan',
            ]);

            $periode = Periode::findOrFail($request->periode_id);

            $query = Indikator::with(['area', 'subArea'])->where('periode_id', $request->periode_id);

            if ($kategori) {
                $query->where('kategori', $kategori);
            }

            $indikators = $query->orderBy('created_at', 'desc')->get();
        }
        return view('admin.indikator.template', compact('periodes', 'periode', 'kategori', 'indikators'));
    }

    public function copyTemplate(Request $request)
    {
        $request->validate([
            'old_periode_id' => 'required|exists:periodes,id',
            'new_periode_id' => 'required|exists:periodes,id|different:old_periode_id',
        ]);

        $oldPeriodeId = $request->old_periode_id;
        $newPeriodeId = $request->new_periode_id;

        $sudahDisalin = Indikator::where('periode_id', $newPeriodeId)
            ->whereIn('pertanyaan', function ($query) use ($oldPeriodeId) {
                $query->select('pertanyaan')->from('indikators')->where('periode_id', $oldPeriodeId);
            })
            ->exists();

        if ($sudahDisalin) {
            return redirect()->back()->with('error', 'template dari periode tersebut telah disalin ke periode tujuan.');
        }

        $indikators = Indikator::where('periode_id', $oldPeriodeId)->where('status', 'published')->get();

        DB::beginTransaction();
        try {
            foreach ($indikators as $indikator) {
                $newIndikator = $indikator->replicate();
                $newIndikator->periode_id = $newPeriodeId;
                $newIndikator->status = 'draft';
                $newIndikator->save();

                foreach ($indikator->opsiJawaban as $opsi) {
                    $newOpsi = $opsi->replicate();
                    $newOpsi->indikator_id = $newIndikator->id;
                    $newOpsi->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Template berhasil disalin ke periode baru. Silakan publish.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menyalin template: ' . $e->getMessage());
        }
    }

    public function bulkPublish(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
            'indikator_ids' => 'required|array',
            'indikator_ids.*' => 'exists:indikators,id',
        ]);

        $periodeId = $request->periode_id;
        $indikatorIds = $request->indikator_ids;

        foreach ($indikatorIds as $indikatorId) {
            Indikator::where('id', $indikatorId)->update(['status' => 'published']);

            DB::table('indikator_periode')->updateOrInsert(['indikator_id' => $indikatorId, 'periode_id' => $periodeId], ['published' => true, 'updated_at' => now(), 'created_at' => now()]);
        }

        return back()->with('success', 'semua indikator berhasil dipublish.');
    }
}
