@extends('layouts.app')
@section('title', 'Monitoring Kementerian')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">Monitoring Bantuan Sosial</h1>
        <p class="text-gray-500 text-sm mt-1">Akses read-only untuk pemantauan data verifikasi bansos.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border p-5 text-center">
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Laporan</div>
        </div>
        <div class="bg-yellow-50 rounded-xl border border-yellow-100 p-5 text-center">
            <div class="text-3xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
            <div class="text-sm text-yellow-600 mt-1">Proses</div>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-100 p-5 text-center">
            <div class="text-3xl font-bold text-green-700">{{ $stats['terverifikasi'] }}</div>
            <div class="text-sm text-green-600 mt-1">Terverifikasi</div>
        </div>
        <div class="bg-red-50 rounded-xl border border-red-100 p-5 text-center">
            <div class="text-3xl font-bold text-red-700">{{ $stats['ditolak'] }}</div>
            <div class="text-sm text-red-600 mt-1">Ditolak</div>
        </div>
    </div>

    {{-- Table (read-only) --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b text-sm font-semibold text-gray-700">Semua Data Laporan</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Pelapor</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Lokasi</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reports as $report)
                        <tr>
                            <td class="px-4 py-3 text-gray-400">{{ $report->id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $report->user->name }}</td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $report->description }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $report->city ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $report->status_badge }}">
                                    {{ $report->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $report->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $reports->links() }}</div>
    </div>

</div>
@endsection
