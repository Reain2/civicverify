<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /** Halaman form buat laporan */
    public function create()
    {
        // Cek batas maksimal 2 laporan per user
        $count = Auth::user()->reports()->count();
        if ($count >= 2) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda sudah mencapai batas maksimal 2 laporan.');
        }

        return view('masyarakat.create');
    }

    /** Simpan laporan baru */
    public function store(Request $request)
    {
        // Validasi batas laporan
        if (Auth::user()->reports()->count() >= 2) {
            return back()->with('error', 'Batas maksimal 2 laporan telah tercapai.');
        }

        $validated = $request->validate([
            'photo'       => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'description' => 'required|string|min:20|max:1000',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'province'    => 'nullable|string|max:100',
            'city'        => 'nullable|string|max:100',
            'district'    => 'nullable|string|max:100',
        ]);

        // Simpan foto
        $photoPath = $request->file('photo')->store('reports', 'public');

        Report::create([
            'user_id'     => Auth::id(),
            'photo'       => $photoPath,
            'description' => $validated['description'],
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'province'    => $validated['province'] ?? null,
            'city'        => $validated['city'] ?? null,
            'district'    => $validated['district'] ?? null,
            'status'      => 'pending',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Laporan berhasil dikirim. Kami akan melakukan verifikasi segera.');
    }

    /** Lihat status laporan sendiri */
    public function myReports()
    {
        $reports = Auth::user()->reports()->latest()->get();
        $reportCount = $reports->count();
        return view('masyarakat.my-reports', compact('reports', 'reportCount'));
    }
}
