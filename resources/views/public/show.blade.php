@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <a href="{{ route('public.index') }}" class="text-blue-600 hover:underline text-sm">← Kembali ke Daftar</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <img src="{{ Storage::url($report->photo) }}" alt="Foto laporan"
             class="w-full max-h-80 object-cover">
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-start">
                <h1 class="text-xl font-bold text-gray-800">Laporan #{{ $report->id }}</h1>
                <span class="text-sm font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700">
                    Terverifikasi
                </span>
            </div>

            <p class="text-gray-700">{{ $report->description }}</p>

            <div class="text-sm text-gray-500 space-y-1">
                @if($report->province)
                    <div>{{ implode(', ', array_filter([$report->district, $report->city, $report->province])) }}</div>
                @endif
                <div>Dilaporkan {{ $report->created_at->translatedFormat('d F Y') }}</div>
            </div>

            @if($report->surveyResult)
                <div class="border-t pt-4">
                    <h2 class="font-semibold text-gray-700 mb-3">Hasil Verifikasi Lapangan</h2>
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 space-y-2">
                        <p>{{ $report->surveyResult->notes }}</p>
                        @if($report->surveyResult->photo)
                            <img src="{{ Storage::url($report->surveyResult->photo) }}"
                                 alt="Foto survei" class="w-48 h-48 rounded-lg object-cover mt-2">
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            Survei dilakukan {{ $report->surveyResult->created_at->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Mini map --}}
            @if($report->latitude && $report->longitude)
                <div>
                    <h2 class="font-semibold text-gray-700 mb-2">Lokasi</h2>
                    <div id="detail-map" class="h-48 rounded-xl overflow-hidden border"></div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($report->latitude && $report->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('detail-map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map)
    .bindPopup('Lokasi laporan #{{ $report->id }}').openPopup();
</script>
@endif
@endpush
