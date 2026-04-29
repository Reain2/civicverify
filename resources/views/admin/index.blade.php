@extends('layouts.app')
@section('title', 'Dashboard Konsultan')

@section('content')
<div class="space-y-5">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Konsultan</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola dan verifikasi laporan bantuan sosial dari masyarakat.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Laporan</div>
        </div>
        <div class="bg-yellow-50 rounded-xl border border-yellow-100 shadow-sm p-5 text-center">
            <div class="text-3xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
            <div class="text-sm text-yellow-600 mt-1">Menunggu</div>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-100 shadow-sm p-5 text-center">
            <div class="text-3xl font-bold text-green-700">{{ $stats['terverifikasi'] }}</div>
            <div class="text-sm text-green-600 mt-1">Terverifikasi</div>
        </div>
        <div class="bg-red-50 rounded-xl border border-red-100 shadow-sm p-5 text-center">
            <div class="text-3xl font-bold text-red-700">{{ $stats['ditolak'] }}</div>
            <div class="text-sm text-red-600 mt-1">Ditolak</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-5 py-3 border-b border-gray-100 flex flex-wrap items-center gap-3">
            <input
                type="text"
                id="table-search"
                placeholder="Cari laporan atau pelapor..."
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-60"
            >
            <select id="filter-status"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="pending">Menunggu</option>
                <option value="terverifikasi">Terverifikasi</option>
                <option value="ditolak">Ditolak</option>
            </select>

            {{-- Bulk assign panel — muncul saat ada yang dicentang --}}
            <div id="bulk-panel" class="hidden flex items-center gap-2 ml-auto">
                <span id="selected-count" class="text-sm text-gray-600 font-medium"></span>
                <select id="bulk-surveyor"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Surveyor</option>
                    @foreach($surveyors as $surveyor)
                        <option value="{{ $surveyor->id }}">{{ $surveyor->name }}</option>
                    @endforeach
                </select>
                <button id="btn-bulk-assign"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded-lg transition disabled:opacity-50"
                        disabled>
                    Tugaskan
                </button>
                <button id="btn-clear-select"
                        class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-3 py-1.5 rounded-lg transition">
                    Batal
                </button>
            </div>

            <div id="normal-action" class="ml-auto text-sm text-gray-400" id="row-count">
                {{ $reports->total() }} laporan
            </div>
        </div>

        {{-- Bulk assign form (hidden, submitted via JS) --}}
        <form id="form-bulk-assign" method="POST" action="{{ route('admin.reports.bulkAssign') }}" class="hidden">
            @csrf
            <input type="hidden" name="surveyor_id" id="form-surveyor-id">
            <div id="form-report-ids"></div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox" id="check-all"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Pelapor</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Lokasi</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Surveyor</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="report-tbody">
                    @forelse($reports as $i => $report)
                        <tr class="hover:bg-gray-50 transition report-row"
                            data-description="{{ strtolower($report->description) }}"
                            data-pelapor="{{ strtolower($report->user->name) }}"
                            data-status="{{ $report->status }}">

                            <td class="px-4 py-3">
                                @if($report->status === 'pending')
                                    <input type="checkbox"
                                           class="row-check rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           value="{{ $report->id }}">
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-400 font-mono text-xs">
                                {{ ($reports->currentPage() - 1) * $reports->perPage() + $i + 1 }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $report->user->name }}
                            </td>

                            <td class="px-4 py-3 text-gray-600 max-w-xs">
                                <span class="line-clamp-2 block">{{ $report->description }}</span>
                            </td>

                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $report->city ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $report->status_badge }}">
                                    {{ $report->status_label }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-500">
                                {{ $report->assignedSurveyor?->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $report->created_at->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3">
                                <a href="{{ route('admin.reports.show', $report) }}"
                                   class="text-xs border border-gray-300 text-gray-600 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-14 text-center text-gray-400">
                                <p class="font-medium text-gray-500">Belum ada laporan masuk</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100">
            {{ $reports->links() }}
        </div>
    </div>
</div>

{{-- ── TOAST NOTIFIKASI ────────────────────────────────────── --}}
<div id="toast" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-3 rounded-xl shadow-lg z-50"></div>
@endsection

@push('scripts')
<script>
// ── Search & filter ───────────────────────────────────────────
document.getElementById('table-search').addEventListener('input', filterTable);
document.getElementById('filter-status').addEventListener('change', filterTable);

function filterTable() {
    const search = document.getElementById('table-search').value.toLowerCase();
    const status = document.getElementById('filter-status').value;
    document.querySelectorAll('.report-row').forEach(row => {
        const matchSearch = !search
            || row.dataset.description.includes(search)
            || row.dataset.pelapor.includes(search);
        const matchStatus = !status || row.dataset.status === status;
        row.classList.toggle('hidden', !(matchSearch && matchStatus));
    });
}

// ── Checkbox logic ────────────────────────────────────────────
const checkAll   = document.getElementById('check-all');
const bulkPanel  = document.getElementById('bulk-panel');
const normalInfo = document.getElementById('normal-action');

checkAll.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.checked = this.checked;
    });
    updateBulkPanel();
});

document.querySelectorAll('.row-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const all   = document.querySelectorAll('.row-check');
        const checked = document.querySelectorAll('.row-check:checked');
        checkAll.indeterminate = checked.length > 0 && checked.length < all.length;
        checkAll.checked       = checked.length === all.length;
        updateBulkPanel();
    });
});

function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
}

function updateBulkPanel() {
    const checked = getChecked();
    if (checked.length > 0) {
        bulkPanel.classList.remove('hidden');
        normalInfo.classList.add('hidden');
        document.getElementById('selected-count').textContent = checked.length + ' laporan dipilih';
    } else {
        bulkPanel.classList.add('hidden');
        normalInfo.classList.remove('hidden');
    }
    updateAssignBtn();
}

// ── Surveyor dropdown → enable assign button ──────────────────
document.getElementById('bulk-surveyor').addEventListener('change', updateAssignBtn);

function updateAssignBtn() {
    const surveyorId = document.getElementById('bulk-surveyor').value;
    const hasChecked = getChecked().length > 0;
    document.getElementById('btn-bulk-assign').disabled = !(surveyorId && hasChecked);
}

// ── Bulk assign submit ────────────────────────────────────────
document.getElementById('btn-bulk-assign').addEventListener('click', function () {
    const surveyorId = document.getElementById('bulk-surveyor').value;
    const reportIds  = getChecked();

    if (!surveyorId) { showToast('Pilih surveyor terlebih dahulu.'); return; }
    if (!reportIds.length) { showToast('Pilih minimal satu laporan.'); return; }

    // Isi hidden form lalu submit
    document.getElementById('form-surveyor-id').value = surveyorId;
    const container = document.getElementById('form-report-ids');
    container.innerHTML = '';
    reportIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'report_ids[]';
        inp.value = id;
        container.appendChild(inp);
    });

    document.getElementById('form-bulk-assign').submit();
});

// ── Clear selection ───────────────────────────────────────────
document.getElementById('btn-clear-select').addEventListener('click', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    checkAll.checked       = false;
    checkAll.indeterminate = false;
    updateBulkPanel();
});

// ── Toast helper ──────────────────────────────────────────────
function showToast(message) {
    const el = document.getElementById('toast');
    el.textContent = message;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3000);
}
</script>
@endpush
