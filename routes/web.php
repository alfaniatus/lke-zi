<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ManagerArea\ManagerAreaController;
use App\Http\Controllers\ManagerArea\ManagerIndikatorController;
use App\Http\Controllers\Admin\IndikatorController;
use App\Http\Controllers\ManagerArea\ManagerJawabanController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\ValidasiJawabanController;
use App\Http\Controllers\ManagerArea\ManagerHasilController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // CRUD Indikator
        Route::get('/indikator/create', [IndikatorController::class, 'create'])->name('indikator.create');
        Route::get('/indikator/list', [IndikatorController::class, 'index'])->name('indikator.index');
        // Route::post('/indikator/{id}/publish', [IndikatorController::class, 'publish'])->name('indikator.publish');
        // Route::post('/indikator/{id}/unpublish', [IndikatorController::class, 'unpublish'])->name('indikator.unpublish');
        Route::patch('/indikator/{id}/toggle-publish', [IndikatorController::class, 'togglePublish'])->name('indikator.toggle-publish');
        Route::post('/indikator', [IndikatorController::class, 'store'])->name('indikator.store');
        Route::get('/indikator/edit/{id}', [IndikatorController::class, 'edit'])->name('indikator.edit');
        Route::put('/indikator/{id}', [IndikatorController::class, 'update'])->name('indikator.update');
        Route::delete('/indikator/{id}', [IndikatorController::class, 'destroy'])->name('indikator.destroy');

        // Route::patch('/indikator/{id}/toggle-publish', [IndikatorController::class, 'togglePublish'])->name('indikator.toggle-publish');
        Route::get('/indikator/template', [TemplateController::class, 'template'])->name('indikator.template');
        Route::post('/indikator/template/publish', [TemplateController::class, 'bulkPublish'])->name('indikator.template.publish');
        Route::post('/indikator/template/copy', [TemplateController::class, 'copyTemplate'])->name('indikator.template.copy');

        Route::get('/sub-area/pemenuhan', [AdminController::class, 'showPemenuhan'])->name('admin.sub-area.pemenuhan');
        Route::get('/sub-area/reform', [AdminController::class, 'showReform'])->name('admin.sub-area.reform');

        Route::get('/validasi', [ValidasiJawabanController::class, 'index'])->name('admin.validasi.index');
        Route::post('/validasi', [ValidasiJawabanController::class, 'simpan'])->name('admin.validasi.simpan');
    });

Route::middleware(['auth', 'role:manager'])
    ->prefix('manager-area')
    ->group(function () {
        Route::get('/dashboard', [ManagerAreaController::class, 'dashboard'])->name('manager-area.dashboard');
        Route::get('/indikator/{area}/{kategori}', [ManagerIndikatorController::class, 'index'])->name('manager-area.indikator.index');
        Route::get('/jawaban/{area}/{kategori}', [ManagerJawabanController::class, 'preview'])->name('manager-area.jawaban.preview');

        Route::post('/jawaban/simpan', [ManagerJawabanController::class, 'store'])->name('manager-area.jawaban.store');
        Route::post('/jawaban/submit/{area}/{kategori}', [ManagerJawabanController::class, 'submit'])->name('manager-area.submit-jawaban');

        Route::get('/hasil', [ManagerHasilController::class, 'index'])->name('manager-area.hasil.index');
        Route::get('/hasil/{id}/edit', [ManagerHasilController::class, 'edit'])->name('manager-area.hasil.edit');
        Route::put('/hasil/{id}', [ManagerHasilController::class, 'update'])->name('manager-area.hasil.update');
        Route::post('/hasil/{id}/submit-ulang', [ManagerHasilController::class, 'submitUlang'])->name('manager-area.hasil.submit-ulang');
    });
