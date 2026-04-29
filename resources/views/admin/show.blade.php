@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <a href="{{ route('admin.reports') }}" class="text-blue-600 hover:underline text-sm">← Kembali ke Daftar</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        <div class="flex justify-between items-start">
            <h1 class="text-xl font-bold text-gray-800">Detail Laporan #{{ $report->id }}</h1>
            <span class="text-sm font-semibold px-3 py-1 rounded-full {{ $report->status_badge }}">
                {{ $report->status_label }}
            </span>
        </div>

        {{-- Foto + info --}}
        <div class="flex gap-5">
            <img src="{{ Storage::url($report->photo) }}" alt="Foto laporan"
                 class="w-40 h-40 rounded-xl object-cover flex-shrink-0 border">
            <div class="space-y-2 text-sm text-gray-600">
                <div><strong class="text-gray-800">Pelapor:</strong> {{ $report->user->name }} ({{ $report->user->email }})</div>
                <div><strong class="text-gray-800">Deskripsi:</strong> {{ $report->description }}</div>
                <div><strong class="text-gray-800">Lokasi:</strong>
                    {{ implode(', ', array_filter([$report->district, $report->city, $report->province])) ?: 'Tidak tersedia' }}
                </div>
                @if($report->latitude)
                    <div><strong class="text-gray-800">Koordinat:</strong> {{ $report->latitude }}, {{ $report->longitude }}</div>
                @endif
                <div><strong class="text-gray-800">Dikirim:</strong> {{ $report->created_at->translatedFormat('d F Y, H:i') }}</div>
            </div>
        </div>

        {{-- Hasil Survei --}}
        @if($report->surveyResult)
            <div class="border-t pt-4">
                <h2 class="font-semibold text-gray-700 mb-3">📋 Hasil Survei Lapangan</h2>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm text-gray-600">
                    <div><strong>Surveyor:</strong> {{ $report->surveyResult->surveyor->name }}</div>
                    <div><strong>Catatan:</strong> {{ $report->surveyResult->notes }}</div>
                    @if($report->surveyResult->photo)
                        <img src="{{ Storage::url($report->surveyResult->photo) }}"
                             alt="Foto survei" class="w-40 h-40 rounded-lg object-cover mt-2">
                    @endif
                    <div><strong>Waktu Survei:</strong> {{ $report->surveyResult->created_at->translatedFormat('d F Y, H:i') }}</div>
                </div>
            </div>
        @endif

        {{-- Alasan penolakan --}}
        @if($report->rejection_reason)
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-sm text-red-700">
                <strong>Alasan Penolakan:</strong> {{ $report->rejection_reason }}
            </div>
        @endif

        {{-- ACTION BUTTONS --}}
        @if($report->status === 'pending')
            <div class="border-t pt-5 space-y-4">

                {{-- Assign surveyor --}}
                @if(!$report->assigned_surveyor_id)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Tindak Lanjut — Tugaskan Surveyor</h3>
                        <form method="POST" action="{{ route('admin.reports.tindakLanjut', $report) }}" class="flex gap-3">
                            @csrf
                            <select name="surveyor_id" required
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Surveyor --</option>
                                @foreach($surveyors as $surveyor)
                                    <option value="{{ $surveyor->id }}">{{ $surveyor->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition text-sm">
                                Tindak Lanjut
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-sm text-gray-600 bg-blue-50 rounded-lg p-3">
                        ✅ Ditugaskan ke: <strong>{{ $report->assignedSurveyor->name }}</strong>
                    </div>
                @endif

                {{-- Validasi akhir (setelah survei ada) --}}
                @if($report->surveyResult)
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('admin.reports.verifikasi', $report) }}">
                            @csrf
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold transition text-sm">
                                ✅ Terverifikasi
                            </button>
                        </form>

                        <button onclick="document.getElementById('modal-tolak-akhir').classList.remove('hidden')"
                                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-semibold transition text-sm">
                            ❌ Tolak
                        </button>
                    </div>
                @else
                    {{-- Tolak screening awal --}}
                    <button onclick="document.getElementById('modal-tolak').classList.remove('hidden')"
                            class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg font-semibold transition text-sm">
                        Tolak Laporan
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- Modal: Tolak (screening) --}}
<div id="modal-tolak" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="font-bold text-gray-800 mb-3">Alasan Penolakan</h3>
        <form method="POST" action="{{ route('admin.reports.tolak', $report) }}">
            @csrf
            <textarea name="rejection_reason" rows="4" required placeholder="Tulis alasan penolakan (min. 10 karakter)..."
                      class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none mb-4"></textarea>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modal-tolak').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Tolak akhir --}}
<div id="modal-tolak-akhir" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="font-bold text-gray-800 mb-3">Tolak Setelah Validasi</h3>
        <form method="POST" action="{{ route('admin.reports.tolakAkhir', $report) }}">
            @csrf
            <textarea name="rejection_reason" rows="4" required placeholder="Tulis alasan penolakan akhir..."
                      class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none mb-4"></textarea>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modal-tolak-akhir').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
