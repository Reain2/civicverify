<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\SurveyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    /** Daftar tugas surveyor */
    public function index()
    {
        $tasks = Report::where('assigned_surveyor_id', Auth::id())
            ->with(['user', 'surveyResult'])
            ->latest()
            ->get();

        return view('surveyor.index', compact('tasks'));
    }

    /** Form input hasil survei */
    public function create(Report $report)
    {
        // Pastikan tugas ini memang milik surveyor ini
        if ($report->assigned_surveyor_id !== Auth::id()) {
            abort(403, 'Tugas ini bukan milik Anda.');
        }

        if ($report->surveyResult) {
            return redirect()->route('surveyor.tasks')
                ->with('info', 'Survei untuk laporan ini sudah pernah diisi.');
        }

        return view('surveyor.create', compact('report'));
    }

    /** Simpan hasil survei dari server */
    public function store(Request $request, Report $report)
    {
        if ($report->assigned_surveyor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'notes'     => 'required|string|min:10',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('survey-results', 'public');
        }

        SurveyResult::create([
            'report_id'   => $report->id,
            'surveyor_id' => Auth::id(),
            'photo'       => $photoPath,
            'notes'       => $validated['notes'],
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'synced'      => true, // langsung sync karena online
        ]);

        return redirect()->route('surveyor.tasks')
            ->with('success', 'Hasil survei berhasil disimpan.');
    }

    /**
     * API endpoint untuk sync data dari localStorage (offline mode).
     * Dipanggil via JS ketika device kembali online.
     */
    public function syncOffline(Request $request)
    {
        $request->validate([
            'surveys'             => 'required|array',
            'surveys.*.report_id' => 'required|exists:reports,id',
            'surveys.*.notes'     => 'required|string',
            'surveys.*.latitude'  => 'nullable|numeric',
            'surveys.*.longitude' => 'nullable|numeric',
        ]);

        $synced = 0;

        foreach ($request->surveys as $data) {
            $report = Report::find($data['report_id']);

            // Hanya izinkan jika memang ditugaskan ke surveyor ini
            if ($report->assigned_surveyor_id !== Auth::id()) {
                continue;
            }

            // Jangan duplikat
            if ($report->surveyResult) {
                continue;
            }

            SurveyResult::create([
                'report_id'   => $data['report_id'],
                'surveyor_id' => Auth::id(),
                'notes'       => $data['notes'],
                'latitude'    => $data['latitude'] ?? null,
                'longitude'   => $data['longitude'] ?? null,
                'synced'      => true,
            ]);

            $synced++;
        }

        return response()->json([
            'success' => true,
            'synced'  => $synced,
            'message' => "{$synced} hasil survei berhasil disinkronkan.",
        ]);
    }
}
