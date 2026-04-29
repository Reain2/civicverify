@extends('layouts.app')
@section('title', 'Tugas Survei')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tugas Survei</h1>
        <p class="text-gray-500 text-sm mt-1">Daftar laporan yang ditugaskan kepada Anda.</p>
    </div>

    {{-- Sync status --}}
    <div id="sync-status" class="hidden bg-green-50 border border-green-200 text-green-800 rounded-xl p-3 text-sm"></div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Search / filter bar --}}
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between gap-3">
            <input
                type="text"
                id="table-search"
                placeholder="Cari laporan..."
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-60"
            >
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span id="row-count">{{ $tasks->count() }} tugas</span>
                <select id="filter-status"
                        class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua status</option>
                    <option value="Belum Disurvei">Belum Disurvei</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="task-table">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left w-8">#</th>
                        <th class="px-4 py-3 text-left">Pelapor</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Lokasi</th>
                        <th class="px-4 py-3 text-left">Tanggal Masuk</th>
                        <th class="px-4 py-3 text-left">Status Survei</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="task-tbody">
                    @forelse($tasks as $i => $task)
                        <tr class="hover:bg-gray-50 transition task-row"
                            data-description="{{ strtolower($task->description) }}"
                            data-pelapor="{{ strtolower($task->user->name) }}"
                            data-status="{{ $task->surveyResult ? 'Selesai' : 'Belum Disurvei' }}">

                            <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $i + 1 }}</td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $task->user->name }}
                            </td>

                            <td class="px-4 py-3 text-gray-600 max-w-xs">
                                <span class="line-clamp-2 block">{{ $task->description }}</span>
                            </td>

                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $task->city ?? '—' }}
                                @if($task->province)
                                    <span class="text-gray-400">, {{ $task->province }}</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $task->created_at->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3">
                                @if($task->surveyResult)
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                        Selesai
                                    </span>
                                @else
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700">
                                        Belum Disurvei
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    {{-- Tombol detail --}}
                                    <button
                                        type="button"
                                        class="btn-detail text-xs border border-gray-300 text-gray-600 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition font-medium"
                                        data-id="{{ $task->id }}"
                                        data-pelapor="{{ $task->user->name }}"
                                        data-email="{{ $task->user->email }}"
                                        data-description="{{ $task->description }}"
                                        data-province="{{ $task->province }}"
                                        data-city="{{ $task->city }}"
                                        data-district="{{ $task->district }}"
                                        data-lat="{{ $task->latitude }}"
                                        data-lng="{{ $task->longitude }}"
                                        data-date="{{ $task->created_at->translatedFormat('d F Y, H:i') }}"
                                        data-photo="{{ $task->photo ? Storage::url($task->photo) : '' }}"
                                        data-surveyed="{{ $task->surveyResult ? '1' : '0' }}"
                                        data-survey-url="{{ route('surveyor.survey.create', $task) }}"
                                    >
                                        Detail
                                    </button>

                                    @if(!$task->surveyResult)
                                        <a href="{{ route('surveyor.survey.create', $task) }}"
                                           class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                                            Input Survei
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">
                                            {{ $task->surveyResult->created_at->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-14 text-center text-gray-400">
                                <div class="text-gray-300 text-4xl mb-3 select-none">—</div>
                                <p class="font-medium text-gray-500">Belum ada tugas</p>
                                <p class="text-xs mt-1">Konsultan belum menugaskan laporan kepada Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->count() > 0)
            <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400">
                Menampilkan {{ $tasks->count() }} laporan yang ditugaskan kepada Anda.
            </div>
        @endif
    </div>
</div>

{{-- ── MODAL DETAIL LAPORAN ───────────────────────────────── --}}
<div id="modal-detail"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800" id="modal-title">Detail Laporan</h2>
            <button id="btn-close-modal"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none font-light">&times;</button>
        </div>

        <div class="px-6 py-5 space-y-4 text-sm">

            {{-- Foto --}}
            <div id="modal-photo-wrapper" class="hidden">
                <img id="modal-photo" src="" alt="Foto laporan"
                     class="w-full max-h-52 object-cover rounded-xl border border-gray-100">
            </div>

            {{-- Data laporan --}}
            <div class="space-y-2 text-gray-600">
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-gray-400 col-span-1">Pelapor</span>
                    <span class="col-span-2 font-medium text-gray-800" id="modal-pelapor"></span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-gray-400 col-span-1">Email</span>
                    <span class="col-span-2" id="modal-email"></span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-gray-400 col-span-1">Deskripsi</span>
                    <span class="col-span-2" id="modal-description"></span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-gray-400 col-span-1">Lokasi</span>
                    <span class="col-span-2" id="modal-lokasi"></span>
                </div>
                <div class="grid grid-cols-3 gap-1" id="modal-coords-row">
                    <span class="text-gray-400 col-span-1">Koordinat</span>
                    <span class="col-span-2 font-mono text-xs" id="modal-coords"></span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-gray-400 col-span-1">Tanggal</span>
                    <span class="col-span-2" id="modal-date"></span>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
            <button id="btn-close-modal-2"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-xl transition">
                Tutup
            </button>
            <a id="modal-survey-link" href="#"
               class="hidden bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition">
                Input Survei
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Sync status ───────────────────────────────────────────────
const syncResult = sessionStorage.getItem('syncResult');
if (syncResult) {
    const el = document.getElementById('sync-status');
    el.textContent = syncResult;
    el.classList.remove('hidden');
    sessionStorage.removeItem('syncResult');
}

// ── Table search & filter ─────────────────────────────────────
document.getElementById('table-search').addEventListener('input', filterTable);
document.getElementById('filter-status').addEventListener('change', filterTable);

function filterTable() {
    const search = document.getElementById('table-search').value.toLowerCase();
    const status = document.getElementById('filter-status').value;
    const rows   = document.querySelectorAll('.task-row');
    let visible  = 0;

    rows.forEach(row => {
        const matchSearch = !search
            || row.dataset.description.includes(search)
            || row.dataset.pelapor.includes(search);
        const matchStatus = !status || row.dataset.status === status;

        if (matchSearch && matchStatus) {
            row.classList.remove('hidden');
            visible++;
        } else {
            row.classList.add('hidden');
        }
    });

    document.getElementById('row-count').textContent = `${visible} tugas`;
}

// ── Modal detail ──────────────────────────────────────────────
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function () {
        const d = this.dataset;

        document.getElementById('modal-title').textContent       = 'Detail Laporan #' + d.id;
        document.getElementById('modal-pelapor').textContent     = d.pelapor;
        document.getElementById('modal-email').textContent       = d.email;
        document.getElementById('modal-description').textContent = d.description;

        const lokasi = [d.district, d.city, d.province].filter(Boolean).join(', ');
        document.getElementById('modal-lokasi').textContent = lokasi || 'Tidak dicantumkan';

        if (d.lat && d.lng) {
            document.getElementById('modal-coords').textContent = d.lat + ', ' + d.lng;
            document.getElementById('modal-coords-row').classList.remove('hidden');
        } else {
            document.getElementById('modal-coords-row').classList.add('hidden');
        }

        document.getElementById('modal-date').textContent = d.date;

        const photoWrapper = document.getElementById('modal-photo-wrapper');
        const photoImg     = document.getElementById('modal-photo');
        if (d.photo) {
            photoImg.src = d.photo;
            photoWrapper.classList.remove('hidden');
        } else {
            photoWrapper.classList.add('hidden');
        }

        const surveyLink = document.getElementById('modal-survey-link');
        if (d.surveyed === '0') {
            surveyLink.href = d.surveyUrl;
            surveyLink.classList.remove('hidden');
        } else {
            surveyLink.classList.add('hidden');
        }

        document.getElementById('modal-detail').classList.remove('hidden');
    });
});

function closeModal() {
    document.getElementById('modal-detail').classList.add('hidden');
}
document.getElementById('btn-close-modal').addEventListener('click', closeModal);
document.getElementById('btn-close-modal-2').addEventListener('click', closeModal);
document.getElementById('modal-detail').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
