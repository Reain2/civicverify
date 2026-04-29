<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CivicVerify') — Sistem Verifikasi Bansos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1d4ed8">
    @stack('head')
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Navbar --}}
<nav class="bg-blue-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-3">
                <span class="text-2xl"></span>
                <a href="{{ route('home') }}" class="font-bold text-lg tracking-wide">CivicVerify</a>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('public.index') }}" class="hover:text-blue-200">Laporan Publik</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-200">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-3 py-1 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-blue-200">Login</a>
                    <a href="{{ route('register') }}" class="bg-white text-blue-700 px-3 py-1 rounded-lg font-semibold hover:bg-blue-50 transition">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Offline indicator --}}
<div id="offline-banner" class="hidden bg-orange-500 text-white text-center py-2 text-sm font-medium">
    Anda sedang offline. Data survei akan disimpan lokal dan otomatis tersinkron saat online kembali.
</div>

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-4 mt-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
            {{ session('info') }}
        </div>
    @endif
</div>

{{-- Main content --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @yield('content')
</main>

<footer class="mt-12 border-t bg-white py-6 text-center text-sm text-gray-500">
    © {{ date('Y') }} CivicVerify — Sistem Verifikasi Bantuan Sosial Berbasis Partisipasi Masyarakat
</footer>

{{-- Offline sync script --}}
<script src="{{ asset('js/offline-sync.js') }}"></script>
@stack('scripts')
</body>
</html>
