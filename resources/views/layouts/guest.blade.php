<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CivicVerify') — Sistem Verifikasi Bansos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 flex flex-col items-center justify-center py-10 px-4">

    <!-- Logo / Branding -->
    <div class="mb-8 text-center">
        <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2 group">
            <span class="text-5xl drop-shadow">🏛️</span>
            <span class="text-white font-bold text-2xl tracking-wide group-hover:text-blue-200 transition">
                CivicVerify
            </span>
            <span class="text-blue-200 text-xs font-medium tracking-widest uppercase">
                Sistem Verifikasi Bantuan Sosial
            </span>
        </a>
    </div>

    <!-- Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Card header accent -->
        <div class="h-1.5 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-400"></div>

        <div class="px-8 py-8">
            {{ $slot }}
        </div>
    </div>

    <!-- Footer note -->
    <p class="mt-8 text-blue-200 text-xs text-center">
        © {{ date('Y') }} CivicVerify &mdash; Partisipasi masyarakat untuk bansos yang tepat sasaran.
    </p>

</body>
</html>
