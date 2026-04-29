@extends('layouts.app')
@section('title', 'Buat Laporan Baru')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="mb-4 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-700 font-medium">Buat Laporan</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <h1 class="text-2xl font-bold text-gray-800 mb-1">Buat Laporan Baru</h1>
        <p class="text-gray-500 text-sm mb-6">
            Laporan Anda akan diverifikasi terlebih dahulu sebelum ditampilkan ke publik.
            Anda dapat mengirim maksimal <strong>2 laporan</strong>.
        </p>

        {{-- Global error summary --}}
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
                <p class="font-semibold mb-1">Mohon periksa kembali isian berikut:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('report.store') }}"
            enctype="multipart/form-data"
            id="report-form"
            class="space-y-6"
        >
            @csrf

            {{-- ── FOTO ─────────────────────────────────────────── --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Foto Bukti
                    <span class="text-red-500">*</span>
                </label>

                {{-- Preview area --}}
                <div id="photo-preview-wrapper" class="hidden mb-3">
                    <img
                        id="photo-preview"
                        src=""
                        alt="Preview foto"
                        class="w-full max-h-64 object-cover rounded-xl border border-gray-200"
                    >
                    <button
                        type="button"
                        id="btn-remove-photo"
                        class="mt-2 text-xs text-red-500 hover:text-red-700 underline"
                    >
                        Hapus foto
                    </button>
                </div>

                <input
                    type="file"
                    name="photo"
                    id="photo-input"
                    accept="image/jpeg,image/png,image/jpg"
                    class="w-full border @error('photo') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                >

                @error('photo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG. Ukuran maks. 5MB.</p>
                @enderror
            </div>

            {{-- ── DESKRIPSI ────────────────────────────────────── --}}
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="description" class="block text-sm font-semibold text-gray-700">
                        Deskripsi Kondisi
                        <span class="text-red-500">*</span>
                    </label>
                    <span id="char-count" class="text-xs text-gray-400">0 / 1000</span>
                </div>
                <textarea
                    name="description"
                    id="description"
                    rows="5"
                    maxlength="1000"
                    placeholder="Jelaskan kondisi penerima bantuan sosial secara lengkap dan jelas. Minimal 20 karakter."
                    class="w-full border @error('description') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-gray-400">Minimal 20 karakter, maksimal 1000 karakter.</p>
                @enderror
            </div>

            {{-- ── LOKASI ───────────────────────────────────────── --}}
            <fieldset class="border border-gray-200 rounded-xl p-5 space-y-4">
                <div class="flex justify-between items-center">
                    <legend class="text-sm font-semibold text-gray-700 px-1">
                        Informasi Lokasi
                        <span class="text-red-500">*</span>
                    </legend>
                    <button
                        type="button"
                        id="btn-gps"
                        class="text-xs bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition font-medium"
                    >
                        Deteksi GPS Otomatis
                    </button>
                </div>

                <p class="text-xs text-gray-400">
                    Koordinat GPS wajib diisi. Gunakan tombol deteksi otomatis atau isi secara manual.
                </p>

                <p id="gps-status" class="hidden text-xs px-3 py-2 rounded-lg"></p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Provinsi</label>
                        <input
                            type="text"
                            name="province"
                            id="province"
                            placeholder="Contoh: Jawa Barat"
                            value="{{ old('province') }}"
                            class="w-full border @error('province') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Kota / Kabupaten</label>
                        <input
                            type="text"
                            name="city"
                            id="city"
                            placeholder="Contoh: Kota Bandung"
                            value="{{ old('city') }}"
                            class="w-full border @error('city') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kecamatan</label>
                    <input
                        type="text"
                        name="district"
                        id="district"
                        placeholder="Contoh: Coblong"
                        value="{{ old('district') }}"
                        class="w-full border @error('district') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

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

            {{-- ── SUBMIT ───────────────────────────────────────── --}}
            <div class="flex gap-3 pt-1">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex-1 text-center border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold py-3 rounded-xl transition text-sm"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    id="btn-submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition shadow text-sm disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    Kirim Laporan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Character counter ─────────────────────────────────────────
const textarea  = document.getElementById('description');
const charCount = document.getElementById('char-count');

function updateCount() {
    const len = textarea.value.length;
    charCount.textContent = `${len} / 1000`;
    charCount.className = len < 20
        ? 'text-xs text-red-500'
        : len > 900
            ? 'text-xs text-orange-500'
            : 'text-xs text-gray-400';
}

updateCount(); // init with old() value
textarea.addEventListener('input', updateCount);

// ── Photo preview ─────────────────────────────────────────────
const photoInput  = document.getElementById('photo-input');
const previewWrap = document.getElementById('photo-preview-wrapper');
const previewImg  = document.getElementById('photo-preview');
const btnRemove   = document.getElementById('btn-remove-photo');

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

btnRemove.addEventListener('click', function () {
    photoInput.value = '';
    previewImg.src   = '';
    previewWrap.classList.add('hidden');
});

// ── GPS detection ─────────────────────────────────────────────
const btnGps = document.getElementById('btn-gps');

btnGps.addEventListener('click', function () {
    if (!navigator.geolocation) {
        showStatus('GPS tidak didukung di browser ini.', 'error');
        return;
    }

    this.textContent = 'Mendeteksi...';
    this.disabled    = true;
    showStatus('Sedang mendeteksi lokasi Anda...', 'loading');

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude.toFixed(8);
            const lng = pos.coords.longitude.toFixed(8);

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            document.getElementById('lat-display').textContent = lat;
            document.getElementById('lng-display').textContent = lng;
            showStatus(
                'Koordinat berhasil didapat. Akurasi sekitar ' + Math.round(pos.coords.accuracy) + ' meter.',
                'success'
            );
            btnGps.textContent = 'Deteksi GPS Otomatis';
            btnGps.disabled    = false;
        },
        (err) => {
            const messages = {
                1: 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan browser.',
                2: 'Lokasi tidak dapat ditentukan. Coba isi koordinat secara manual.',
                3: 'Waktu habis saat mendeteksi lokasi. Coba lagi.',
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

// ── Loading state on submit ───────────────────────────────────
document.getElementById('report-form').addEventListener('submit', function () {
    const btn     = document.getElementById('btn-submit');
    btn.textContent = 'Mengirim laporan...';
    btn.disabled    = true;
});
</script>
@endpush
