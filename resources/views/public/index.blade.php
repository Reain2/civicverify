@extends('layouts.app')
@section('title', 'Laporan Terverifikasi')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Terverifikasi</h1>
        <p class="text-gray-500 text-sm mt-1">
            Data laporan bantuan sosial yang telah melalui proses verifikasi penuh.
        </p>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('public.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Provinsi</label>
            <select name="province" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Provinsi</option>
                @foreach($provinces as $prov)
                    <option value="{{ $prov }}" @selected(request('province') === $prov)>{{ $prov }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Kota / Kabupaten</label>
            <select name="city" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kota</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Kecamatan</label>
            <select name="district" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kecamatan</option>
                @foreach($districts as $dist)
                    <option value="{{ $dist }}" @selected(request('district') === $dist)>{{ $dist }}</option>
                @endforeach
            </select>
        </div>
        @if(request()->anyFilled(['province','city','district']))
            <a href="{{ route('public.index') }}"
               class="text-sm text-red-500 hover:underline py-2">Reset Filter</a>
        @endif
    </form>

    {{-- Map --}}
    @if($mapData->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 text-sm font-semibold text-gray-700">
                Peta Sebaran Laporan
            </div>
            <div id="map" class="h-64 w-full bg-gray-200 flex items-center justify-center text-gray-400 text-sm">
                Memuat peta...
            </div>
        </div>
    @endif

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($reports as $report)
            <a href="{{ route('public.show', $report) }}"
               class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition overflow-hidden group">
                <img src="{{ Storage::url($report->photo) }}"
                     alt="Foto laporan"
                     class="w-full h-44 object-cover group-hover:brightness-95 transition">
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                            Terverifikasi
                        </span>
                        <span class="text-xs text-gray-400">{{ $report->created_at->format('d/m/Y') }}</span>
                    </div>
                    <p class="text-sm text-gray-700 line-clamp-2">{{ $report->description }}</p>
                    @if($report->province)
                        <p class="text-xs text-gray-400">
                            {{ implode(', ', array_filter([$report->district, $report->city, $report->province])) }}
                        </p>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <div class="text-5xl mb-4"></div>
                <p class="text-lg font-medium">Belum ada laporan terverifikasi</p>
                <p class="text-sm mt-1">Coba hapus filter atau periksa kembali nanti.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    {{ $reports->links() }}

</div>
@endsection

@push('scripts')
@if($mapData->isNotEmpty())
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const mapData = @json($mapData);

const map = L.map('map').setView([-2.5489, 118.0149], 5); // Indonesia center

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

mapData.forEach(item => {
    if (item.latitude && item.longitude) {
        L.marker([item.latitude, item.longitude])
            .addTo(map)
            .bindPopup(`<strong>Laporan #${item.id}</strong><br>${item.description.substring(0, 80)}...<br><small>${item.city || ''}, ${item.province || ''}</small>`);
    }
});
</script>
@endif
@endpush
