@extends('layouts.app')
@section('title', 'Laporan Saya')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Saya</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ $reportCount }} dari 2 laporan telah dikirim
            </p>
        </div>
        @if($reportCount < 2)
            <a href="{{ route('report.create') }}"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold transition shadow text-sm">
                Buat Laporan Baru
            </a>
        @endif
    </div>

    {{-- Status legend --}}
    <div class="bg-white border border-gray-100 rounded-xl p-4 flex flex-wrap gap-4 text-xs">
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span>
            <span class="text-gray-600">Menunggu Verifikasi — laporan sedang diproses konsultan</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
            <span class="text-gray-600">Terverifikasi — laporan valid dan tampil ke publik</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>
            <span class="text-gray-600">Ditolak — laporan tidak memenuhi syarat verifikasi</span>
        </div>
    </div>

    {{-- List --}}
    @forelse($reports as $report)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex gap-0">

                {{-- Foto --}}
                <div class="w-36 flex-shrink-0">
                    <img
                        src="{{ Storage::url($report->photo) }}"
                        alt="Foto laporan #{{ $report->id }}"
                        class="w-full h-full object-cover"
                        style="min-height: 130px;"
                    >
                </div>

                {{-- Konten --}}
                <div class="flex-1 p-5 min-w-0">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <span class="text-xs text-gray-400 font-mono">Laporan #{{ $report->id }}</span>
                        <span class="flex-shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full {{ $report->status_badge }}">
                            {{ $report->status_label }}
                        </span>
                    </div>

                    <p class="text-gray-800 text-sm line-clamp-3 mb-3">
                        {{ $report->description }}
                    </p>

                    <div class="text-xs text-gray-400 space-y-1">
                        @if($report->province)
                            <div>
                                Lokasi:
                                {{ implode(', ', array_filter([$report->district, $report->city, $report->province])) }}
                            </div>
                        @else
                            <div>Lokasi tidak dicantumkan</div>
                        @endif
                        <div>Dikirim {{ $report->created_at->translatedFormat('d F Y') }}</div>
                    </div>

                    {{-- Status-specific notes --}}
                    @if($report->status === 'ditolak' && $report->rejection_reason)
                        <div class="mt-3 bg-red-50 border border-red-100 rounded-lg px-3 py-2 text-xs text-red-700">
                            <span class="font-semibold">Alasan penolakan:</span>
                            {{ $report->rejection_reason }}
                        </div>
                    @elseif($report->status === 'pending' && $report->assigned_surveyor_id)
                        <div class="mt-3 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 text-xs text-blue-700">
                            Sedang dalam proses survei lapangan oleh petugas.
                        </div>
                    @elseif($report->status === 'pending')
                        <div class="mt-3 bg-yellow-50 border border-yellow-100 rounded-lg px-3 py-2 text-xs text-yellow-700">
                            Menunggu ditinjau oleh konsultan.
                        </div>
                    @elseif($report->status === 'terverifikasi')
                        <div class="mt-3 bg-green-50 border border-green-100 rounded-lg px-3 py-2 text-xs text-green-700">
                            Laporan ini telah diverifikasi dan dapat dilihat oleh publik.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">Belum ada laporan</p>
            <p class="text-gray-400 text-sm mt-1">Laporan yang Anda kirim akan muncul di sini.</p>
            <a href="{{ route('report.create') }}"
               class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                Buat Laporan Sekarang
            </a>
        </div>
    @endforelse

</div>
@endsection
