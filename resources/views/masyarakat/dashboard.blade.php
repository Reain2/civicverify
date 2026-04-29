@extends('layouts.app')
@section('title', 'Dashboard Saya')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, {{ auth()->user()->name }}</h1>
            <p class="text-gray-500 text-sm mt-1">Pantau status laporan bantuan sosial Anda</p>
        </div>
        @if(auth()->user()->reports()->count() < 2)
            <a href="{{ route('report.create') }}"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold transition shadow text-sm text-center">
                Buat Laporan Baru
            </a>
        @else
            <span class="text-sm text-gray-400 italic">Batas 2 laporan telah tercapai</span>
        @endif
    </div>

    {{-- Quota info --}}
    @php $reportCount = auth()->user()->reports()->count(); @endphp
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold text-blue-800 text-sm">Kuota Laporan Anda</div>
                <div class="text-blue-600 text-xs mt-0.5">Setiap warga dapat mengirim maksimal 2 laporan.</div>
            </div>
            <div class="text-right">
                <span class="text-2xl font-bold text-blue-700">{{ $reportCount }}</span>
                <span class="text-blue-400 font-medium"> / 2</span>
            </div>
        </div>
        <div class="mt-3 w-full bg-blue-200 rounded-full h-1.5">
            <div
                class="bg-blue-600 h-1.5 rounded-full transition-all"
                style="width: {{ ($reportCount / 2) * 100 }}%"
            ></div>
        </div>
    </div>

    {{-- Reports list --}}
    <div class="space-y-4">
        @forelse($reports as $report)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex gap-5">
                <img
                    src="{{ Storage::url($report->photo) }}"
                    alt="Foto laporan"
                    class="w-28 h-28 rounded-lg object-cover flex-shrink-0 bg-gray-100"
                >
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-gray-800 font-medium text-sm line-clamp-2 flex-1">
                            {{ $report->description }}
                        </p>
                        <span class="flex-shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full {{ $report->status_badge }}">
                            {{ $report->status_label }}
                        </span>
                    </div>

                    <div class="mt-2 text-xs text-gray-400 space-y-1">
                        @if($report->province)
                            <div>
                                Lokasi: {{ implode(', ', array_filter([$report->district, $report->city, $report->province])) }}
                            </div>
                        @endif
                        <div>Dikirim pada {{ $report->created_at->translatedFormat('d F Y, H:i') }}</div>
                    </div>

                    @if($report->status === 'ditolak' && $report->rejection_reason)
                        <div class="mt-3 bg-red-50 border border-red-100 rounded-lg px-3 py-2 text-xs text-red-700">
                            <span class="font-semibold">Alasan penolakan:</span>
                            {{ $report->rejection_reason }}
                        </div>
                    @endif

                    @if($report->status === 'pending')
                        <div class="mt-3 bg-yellow-50 border border-yellow-100 rounded-lg px-3 py-2 text-xs text-yellow-700">
                            Laporan Anda sedang dalam antrian verifikasi oleh tim konsultan.
                        </div>
                    @endif
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
                <p class="text-gray-400 text-sm mt-1">Mulai kirim laporan kondisi bantuan sosial di sekitar Anda.</p>
                <a href="{{ route('report.create') }}"
                   class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                    Buat Laporan Pertama
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection
