<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** Dashboard konsultan — semua laporan masuk */
    public function index()
    {
        $reports = Report::with(['user', 'surveyResult', 'assignedSurveyor'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total'         => Report::count(),
            'pending'       => Report::where('status', 'pending')->count(),
            'terverifikasi' => Report::where('status', 'terverifikasi')->count(),
            'ditolak'       => Report::where('status', 'ditolak')->count(),
        ];

        $surveyors = User::where('role', 'surveyor')->orderBy('name')->get();

        return view('admin.index', compact('reports', 'stats', 'surveyors'));
    }

    /** Bulk assign beberapa laporan ke satu surveyor */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'report_ids'   => 'required|array|min:1',
            'report_ids.*' => 'exists:reports,id',
            'surveyor_id'  => 'required|exists:users,id',
        ]);

        $surveyor = User::findOrFail($request->surveyor_id);
        if ($surveyor->role !== 'surveyor') {
            return back()->with('error', 'User yang dipilih bukan surveyor.');
        }

        $updated = Report::whereIn('id', $request->report_ids)
            ->where('status', 'pending')
            ->update(['assigned_surveyor_id' => $surveyor->id]);

        return back()->with('success', "{$updated} laporan berhasil ditugaskan ke {$surveyor->name}.");
    }

    /** Detail satu laporan */
    public function show(Report $report)
    {
        $report->load(['user', 'surveyResult.surveyor', 'assignedSurveyor']);
        $surveyors = User::where('role', 'surveyor')->get();
        return view('admin.show', compact('report', 'surveyors'));
    }

    /** Tindak lanjuti laporan → assign surveyor */
    public function tindakLanjut(Request $request, Report $report)
    {
        $request->validate([
            'surveyor_id' => 'required|exists:users,id',
        ]);

        $surveyor = User::findOrFail($request->surveyor_id);
        if ($surveyor->role !== 'surveyor') {
            return back()->with('error', 'User yang dipilih bukan surveyor.');
        }

        $report->update([
            'assigned_surveyor_id' => $request->surveyor_id,
        ]);

        return back()->with('success', "Laporan berhasil ditugaskan ke surveyor {$surveyor->name}.");
    }

    /** Tolak laporan (screening awal) */
    public function tolak(Request $request, Report $report)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $report->update([
            'status'           => 'ditolak',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Laporan berhasil ditolak.');
    }

    /** Validasi akhir — terverifikasi */
    public function verifikasi(Report $report)
    {
        if (! $report->surveyResult) {
            return back()->with('error', 'Laporan belum memiliki hasil survei lapangan.');
        }

        $report->update(['status' => 'terverifikasi']);

        return back()->with('success', 'Laporan berhasil diverifikasi dan akan tampil ke publik.');
    }

    /** Validasi akhir — ditolak setelah survei */
    public function tolakAkhir(Request $request, Report $report)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $report->update([
            'status'           => 'ditolak',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Laporan ditolak setelah validasi akhir.');
    }
}
