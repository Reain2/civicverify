@extends('layouts.app')
@section('title', 'Input Hasil Survei')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-4 text-sm text-gray-500">
        <a href="{{ route('surveyor.tasks') }}" class="hover:text-blue-600 transition">Tugas Survei</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-700 font-medium">Input Survei #{{ $report->id }}</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4 space-y-5">
        <h1 class="text-xl font-bold text-gray-800">Input Hasil Survei</h1>

        {{-- Info laporan dari masyarakat --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm space-y-1 text-gray-600">
            <div class="font-semibold text-gray-700 mb-1">Laporan #{{ $report->id }} — Data dari Masyarakat</div>
            <div><span class="text-gray-400">Pelapor:</span> {{ $report->user->name }}</div>
            <div><span class="text-gray-400">Deskripsi:</span> {{ $report->description }}</div>
            <div>
                <span class="text-gray-400">Lokasi:</span>
                {{ implode(', ', array_filter([$report->district, $report->city, $report->province])) ?: 'Tidak dicantumkan' }}
            </div>
            @if($report->latitude)
                <div><span class="text-gray-400">Koordinat:</span> {{ $report->latitude }}, {{ $report->longitude }}</div>
            @endif
        </div>

        {{-- Offline notice --}}
        <div id="offline-note" class="hidden bg-orange-50 border border-orange-200 text-orange-700 rounded-lg p-3 text-sm">
            Anda sedang offline. Data akan disimpan di perangkat dan otomatis terkirim saat online kembali.
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                <p class="font-semibold mb-1">Mohon periksa kembali isian berikut:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="survey-form" method="POST"
              action="{{ route('surveyor.survey.store', $report) }}"
              enctype="multipart/form-data"
              class="space-y-5"
              data-report-id="{{ $report->id }}">
            @csrf

            {{-- Foto hasil survei --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Foto Survei Lapangan
                    <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div id="photo-preview-wrapper" class="hidden mb-2">
                    <img id="photo-preview" src="" alt="Preview"
                         class="w-full max-h-48 object-cover rounded-xl border border-gray-200">
                    <button type="button" id="btn-remove-photo"
                            class="mt-1 text-xs text-red-500 hover:text-red-700 underline">
                        Hapus foto
                    </button>
                </div>
                <input type="file" name="photo" id="photo-input"
                       accept="image/jpeg,image/png,image/jpg"
                       class="w-full border @error('photo') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('photo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG. Maks. 5MB.</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="notes-input" class="block text-sm font-semibold text-gray-700">
                        Catatan Hasil Survei <span class="text-red-500">*</span>
                    </label>
                    <span id="notes-count" class="text-xs text-gray-400">0 / 2000</span>
                </div>
                <textarea name="notes" id="notes-input" rows="5" required maxlength="2000"
                          placeholder="Deskripsikan kondisi aktual di lapangan secara detail. Minimal 10 karakter."
                          class="w-full border @error('notes') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- GPS — WAJIB --}}
<fieldset class="border border-gray-200 rounded-xl p-5 space-y-3">
    <div class="flex justify-between items-center">
        <legend class="text-sm font-semibold text-gray-700 px-1">
            Koordinat GPS Lokasi Survei <span class="text-red-500">*</span>
        </legend>
        <button type="button" id="btn-gps"
                class="text-xs bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition font-medium">
            Deteksi GPS Otomatis
        </button>
    </div>

    <p class="text-xs text-gray-400">
        Koordinat diambil otomatis dari GPS. Tidak dapat diinput manual.
    </p>

    <p id="gps-status" class="hidden text-xs px-3 py-2 rounded-lg"></p>

    <!-- HIDDEN INPUT (ANTI EDIT USER) -->
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <!-- DISPLAY SAJA -->
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">
                Latitude <span class="text-red-500">*</span>
            </label>
            <div id="lat-display"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-700">
                Belum terdeteksi
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">
                Longitude <span class="text-red-500">*</span>
            </label>
            <div id="lng-display"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-700">
                Belum terdeteksi
            </div>
        </div>
    </div>
</fieldset>

            <div class="flex gap-3 pt-1">
                <a href="{{ route('surveyor.tasks') }}"
                   class="flex-1 text-center border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold py-3 rounded-xl transition text-sm">
                    Batal
                </a>
                <button type="submit" id="submit-btn"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition shadow text-sm disabled:opacity-60 disabled:cursor-not-allowed">
                    Simpan Hasil Survei
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const reportId = {{ $report->id }};

// ── Notes counter ─────────────────────────────────────────────
const notesInput = document.getElementById('notes-input');
const notesCount = document.getElementById('notes-count');

function updateNotesCount() {
    const len = notesInput.value.length;
    notesCount.textContent = `${len} / 2000`;
    notesCount.className = len < 10 ? 'text-xs text-red-500' : 'text-xs text-gray-400';
}
updateNotesCount();
notesInput.addEventListener('input', updateNotesCount);

// ── Photo preview ─────────────────────────────────────────────
const photoInput  = document.getElementById('photo-input');
const previewWrap = document.getElementById('photo-preview-wrapper');
const previewImg  = document.getElementById('photo-preview');

photoInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 5MB.');
        this.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImg.src = e.target.result;
        previewWrap.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});

document.getElementById('btn-remove-photo').addEventListener('click', function () {
    photoInput.value = '';
    previewImg.src   = '';
    previewWrap.classList.add('hidden');
});

// ── GPS ───────────────────────────────────────────────────────
const btnGps = document.getElementById('btn-gps');

btnGps.addEventListener('click', function () {
    if (!navigator.geolocation) {
        showStatus('GPS tidak didukung di browser ini. Isi koordinat secara manual.', 'error');
        return;
    }
    btnGps.textContent = 'Mendeteksi...';
    btnGps.disabled    = true;
    showStatus('Sedang mendeteksi koordinat lokasi Anda...', 'loading');

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude.toFixed(8);
            const lng = pos.coords.longitude.toFixed(8);

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            document.getElementById('lat-display').textContent = lat;
            document.getElementById('lng-display').textContent = lng;
            showStatus('Koordinat berhasil didapat. Akurasi sekitar ' + Math.round(pos.coords.accuracy) + ' meter.', 'success');
            btnGps.textContent = 'Deteksi GPS Otomatis';
            btnGps.disabled    = false;
        },
        (err) => {
            const messages = {
                1: 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan browser, atau isi koordinat manual.',
                2: 'Lokasi tidak dapat ditentukan. Isi koordinat secara manual.',
                3: 'Waktu deteksi habis. Coba lagi atau isi manual.',
            };
            showStatus(messages[err.code] || err.message, 'error');
            btnGps.textContent = 'Deteksi GPS Otomatis';
            btnGps.disabled    = false;
        },
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
    );
});

function showStatus(message, type) {
    const el = document.getElementById('gps-status');
    const classes = {
        success : 'text-xs px-3 py-2 rounded-lg bg-green-50 text-green-700 border border-green-100',
        error   : 'text-xs px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-100',
        loading : 'text-xs px-3 py-2 rounded-lg bg-blue-50 text-blue-600 border border-blue-100',
    };
    el.textContent = message;
    el.className   = classes[type];
    el.classList.remove('hidden');
}

// ── Offline handling ──────────────────────────────────────────
document.getElementById('survey-form').addEventListener('submit', function (e) {
    if (!navigator.onLine) {
        e.preventDefault();

        const notes = notesInput.value.trim();
        const lat   = document.getElementById('latitude').value;
        const lng   = document.getElementById('longitude').value;

        if (!notes || notes.length < 10) {
            alert('Catatan minimal 10 karakter.');
            return;
        }
        if (!lat || !lng) {
            alert('Koordinat GPS wajib diisi.');
            return;
        }

        window.OfflineSync.saveSurvey({
            report_id: reportId,
            notes,
            latitude:  parseFloat(lat),
            longitude: parseFloat(lng),
        });

        sessionStorage.setItem('syncResult', 'Data survei disimpan offline. Akan otomatis terkirim saat online.');
        window.location.href = '{{ route("surveyor.tasks") }}';
    }
});

// ── Loading state ─────────────────────────────────────────────
document.getElementById('survey-form').addEventListener('submit', function () {
    if (navigator.onLine) {
        const btn     = document.getElementById('submit-btn');
        btn.textContent = 'Menyimpan...';
        btn.disabled    = true;
    }
});

if (!navigator.onLine) {
    document.getElementById('offline-note').classList.remove('hidden');
}
window.addEventListener('offline', () => document.getElementById('offline-note').classList.remove('hidden'));
window.addEventListener('online',  () => document.getElementById('offline-note').classList.add('hidden'));
</script>
@endpush
