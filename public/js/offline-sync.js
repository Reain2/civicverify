/**
 * CivicVerify — Offline Survey Sync
 * public/js/offline-sync.js
 *
 * Cara kerja:
 * 1. Surveyor isi form di halaman surveyor/create
 * 2. Jika offline, data disimpan ke localStorage
 * 3. Saat online kembali, data otomatis di-POST ke /api/survey/sync
 * 4. localStorage dibersihkan setelah sync berhasil
 */

const STORAGE_KEY = 'civicverify_offline_surveys';

// ── Namespace publik ─────────────────────────────────────────────
window.OfflineSync = {
    /**
     * Simpan satu data survei ke localStorage
     * @param {Object} survey { report_id, notes, latitude, longitude }
     */
    saveSurvey(survey) {
        const stored = this.getAll();
        // Jangan duplikat — overwrite kalau report_id sama
        const idx = stored.findIndex(s => s.report_id === survey.report_id);
        if (idx >= 0) {
            stored[idx] = survey;
        } else {
            stored.push(survey);
        }
        localStorage.setItem(STORAGE_KEY, JSON.stringify(stored));
        console.log('[OfflineSync] Saved survey for report_id:', survey.report_id);
    },

    /** Ambil semua data offline */
    getAll() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        } catch {
            return [];
        }
    },

    /** Hapus data yang sudah berhasil di-sync */
    clear() {
        localStorage.removeItem(STORAGE_KEY);
    },

    /** Kirim semua data offline ke server */
    async sync() {
        const surveys = this.getAll();
        if (surveys.length === 0) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.warn('[OfflineSync] CSRF token not found, aborting sync.');
            return;
        }

        console.log('[OfflineSync] Syncing', surveys.length, 'offline surveys...');

        try {
            const response = await fetch('/api/survey/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ surveys }),
            });

            if (response.ok) {
                const data = await response.json();
                console.log('[OfflineSync] Sync success:', data.message);
                this.clear();

                // Simpan pesan ke sessionStorage supaya halaman bisa tampilkan notifikasi
                sessionStorage.setItem(
                    'syncResult',
                    `✅ ${data.message}`
                );

                // Kalau di halaman tugas, reload untuk update tampilan
                if (window.location.pathname.includes('/surveyor/tasks')) {
                    window.location.reload();
                }
            } else {
                console.warn('[OfflineSync] Server responded with error:', response.status);
            }
        } catch (err) {
            console.error('[OfflineSync] Sync failed:', err);
        }
    },
};

// ── Event listeners ──────────────────────────────────────────────

/** Saat online kembali → langsung sync */
window.addEventListener('online', () => {
    console.log('[OfflineSync] Back online, attempting sync...');
    hideBanner();
    OfflineSync.sync();
});

/** Saat offline → tampilkan banner */
window.addEventListener('offline', () => {
    showBanner();
});

/** Cek state awal saat halaman load */
document.addEventListener('DOMContentLoaded', () => {
    if (!navigator.onLine) {
        showBanner();
    } else {
        hideBanner();
        // Sync data lama kalau ada (misal baru balik online dan page di-refresh)
        OfflineSync.sync();
    }
});

// ── Banner helpers ───────────────────────────────────────────────
function showBanner() {
    const el = document.getElementById('offline-banner');
    if (el) el.classList.remove('hidden');
}

function hideBanner() {
    const el = document.getElementById('offline-banner');
    if (el) el.classList.add('hidden');
}
