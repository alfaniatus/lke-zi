<?php

namespace App\Http\Controllers\ManagerArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ManagerAreaController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        return view('manager-area.dashboard', [
            'role' => 'manager',
            'areaUser' => $user->area,
            'areaId' => $user->area_id,
            'currentKategori' => $request->kategori,
        ]);
    }
    public function hasil(Request $request)
    {
        $user = Auth::user();

        return view('manager-area.hasil.index', [
            'routeName' => 'manager-area.hasil',
            'role' => 'manager',
            'areaUser' => $user->area,
            'areaId' => $user->area_id,
            'currentKategori' => $request->kategori,
        ]);
    }
} 