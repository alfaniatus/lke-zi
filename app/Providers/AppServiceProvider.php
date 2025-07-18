<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use App\Models\Periode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       View::composer('*', function ($view) {
            // ⏳ Master Periode Default (2025)
            $masterPeriode = Periode::where('tahun', 2025)->first();
            $view->with('masterPeriode', $masterPeriode);

            // ✅ Hanya lanjut jika user sudah login
            if (Auth::check()) {
                $user = Auth::user();
                $role = $user->role;
                $area = $user->area; // relasi area
                $areaId = $user->area_id;

                // 🔧 Ambil angka setelah "Area " dari nama area
                $areaUser = $area?->name ? Str::after(strtolower($area->name), 'area ') : null;

                $routeName = Request::route()?->getName();

                // 🧭 Sidebar active detection
                $indikatorActive = Str::startsWith($routeName, 'indikator.') || Str::startsWith($routeName, 'admin.indikator');
                $validasiActive = Str::startsWith($routeName, 'validasi.') || Str::startsWith($routeName, 'admin.validasi');
                $dashboardActive = $routeName === ($role === 'admin' ? 'admin.dashboard' : 'manager-area.dashboard');

                // 📦 Share ke semua view
                $view->with(compact(
                    'user', 'role', 'area', 'areaId', 'areaUser',
                    'routeName', 'indikatorActive', 'dashboardActive', 'validasiActive'
                ));
            }
        });
    }
}