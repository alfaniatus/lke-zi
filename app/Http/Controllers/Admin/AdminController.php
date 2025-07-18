<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Indikator;

class AdminController extends Controller
{
    // Dashboard Admin
    public function dashboard()
    {
        return view('admin.dashboard', [
            'routeName' => 'admin.dashboard',
        ]);
    }

    public function showPemenuhan(Request $request)
    {
        $area = (int) $request->query('area', 1);
        return view('admin.sub-area.pemenuhan', [
            'currentKategori' => 'pemenuhan',
            'areaId' => $area,
        ]);
    }
    public function showReform(Request $request)
    {
        $area = (int) $request->query('area', 1);
        return view('admin.sub-area.reform', [
            'currentKategori' => 'reform',
            'areaId' => $area,
        ]);
    }

}
