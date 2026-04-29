<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

// ────────────────────────────────────────────────────────────────
// PUBLIC — Tidak butuh login
// ────────────────────────────────────────────────────────────────
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/public', [PublicController::class, 'index'])->name('public.index');
Route::get('/public/{report}', [PublicController::class, 'show'])->name('public.show');

// ────────────────────────────────────────────────────────────────
// AUTH — Laravel Breeze
// ────────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ────────────────────────────────────────────────────────────────
// AUTHENTICATED — Semua role
// ────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard (redirect berdasar role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── MASYARAKAT ──────────────────────────────────────────────
    Route::middleware('role:masyarakat')->group(function () {
        Route::get('/report/create', [ReportController::class, 'create'])->name('report.create');
        Route::post('/report', [ReportController::class, 'store'])->name('report.store');
        Route::get('/my-reports', [ReportController::class, 'myReports'])->name('report.my');
    });

    // ── KONSULTAN ───────────────────────────────────────────────
    Route::middleware('role:konsultan')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reports', [AdminController::class, 'index'])->name('reports');
        Route::get('/reports/{report}', [AdminController::class, 'show'])->name('reports.show');
        Route::post('/reports/bulk-assign', [AdminController::class, 'bulkAssign'])->name('reports.bulkAssign');
        Route::post('/reports/{report}/tindak-lanjut', [AdminController::class, 'tindakLanjut'])->name('reports.tindakLanjut');
        Route::post('/reports/{report}/tolak', [AdminController::class, 'tolak'])->name('reports.tolak');
        Route::post('/reports/{report}/verifikasi', [AdminController::class, 'verifikasi'])->name('reports.verifikasi');
        Route::post('/reports/{report}/tolak-akhir', [AdminController::class, 'tolakAkhir'])->name('reports.tolakAkhir');
    });

    // ── SURVEYOR ────────────────────────────────────────────────
    Route::middleware('role:surveyor')->prefix('surveyor')->name('surveyor.')->group(function () {
        Route::get('/tasks', [SurveyController::class, 'index'])->name('tasks');
        Route::get('/tasks/{report}/survey', [SurveyController::class, 'create'])->name('survey.create');
        Route::post('/tasks/{report}/survey', [SurveyController::class, 'store'])->name('survey.store');
    });

    // ── API: Sync offline data ──────────────────────────────────
    Route::middleware('role:surveyor')
        ->post('/api/survey/sync', [SurveyController::class, 'syncOffline'])
        ->name('survey.sync');

    // ── KEMENTERIAN (read-only) ─────────────────────────────────
    Route::middleware('role:kementerian')->prefix('kementerian')->name('kementerian.')->group(function () {
        Route::get('/', function () {
            $stats = [
                'total'         => \App\Models\Report::count(),
                'pending'       => \App\Models\Report::where('status', 'pending')->count(),
                'terverifikasi' => \App\Models\Report::where('status', 'terverifikasi')->count(),
                'ditolak'       => \App\Models\Report::where('status', 'ditolak')->count(),
            ];
            $reports = \App\Models\Report::with('user')->latest()->paginate(20);
            return view('kementerian.index', compact('stats', 'reports'));
        })->name('index');
    });
});
