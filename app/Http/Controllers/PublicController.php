<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /** Halaman publik — hanya tampilkan yang terverifikasi */
    public function index(Request $request)
    {
        $query = Report::verified()
            ->with('user')
            ->latest();

        // Filter lokasi
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        $reports = $query->paginate(12)->withQueryString();

        // Data untuk dropdown filter
        $provinces = Report::verified()->whereNotNull('province')
            ->distinct()->pluck('province')->sort()->values();

        $cities = Report::verified()->whereNotNull('city')
            ->when($request->province, fn($q) => $q->where('province', $request->province))
            ->distinct()->pluck('city')->sort()->values();

        $districts = Report::verified()->whereNotNull('district')
            ->when($request->city, fn($q) => $q->where('city', $request->city))
            ->distinct()->pluck('district')->sort()->values();

        // Data untuk peta (semua terverifikasi yang punya koordinat)
        $mapData = Report::verified()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'description', 'latitude', 'longitude', 'province', 'city')
            ->get();

        return view('public.index', compact(
            'reports', 'provinces', 'cities', 'districts', 'mapData'
        ));
    }

    /** Detail laporan terverifikasi */
    public function show(Report $report)
    {
        if ($report->status !== 'terverifikasi') {
            abort(404);
        }
        $report->load(['user', 'surveyResult']);
        return view('public.show', compact('report'));
    }
}
