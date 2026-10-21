<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel — Rackly Billiard Club')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAF9] text-gray-800 antialiased min-h-screen">

    <div class="flex min-h-screen">

        <!-- Sidebar Samping Gelap -->
        <aside class="w-64 bg-[#0E1615] text-white flex flex-col justify-between shrink-0 shadow-xl min-h-screen sticky top-0 h-screen overflow-y-auto">
            <div>
                <!-- Brand / Logo -->
                <div class="p-6 border-b border-white/5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#1B5E43] text-white flex items-center justify-center font-bold text-xl shadow-md">
                            8
                        </div>
                        <div>
                            <span class="text-xl font-black tracking-tight text-white block leading-tight">RACKLY</span>
                            <span class="text-[10px] tracking-widest text-emerald-400 font-bold block uppercase -mt-0.5">Billiard Club</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="px-4 py-6">
                    <span class="block px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">
                        RUANG ADMIN
                    </span>

                    <nav class="space-y-1.5">
                        <!-- 1. Dashboard -->
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#1B5E43] text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Dashboard
                        </a>

                        <!-- 2. Kelola Pesanan -->
                        <a href="{{ route('admin.bookings') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.bookings*') ? 'bg-[#1B5E43] text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.bookings*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Kelola pesanan
                        </a>

                        <!-- 3. Tambah Pesanan Walk-in -->
                        <a href="{{ route('admin.walkin.create') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.walkin*') ? 'bg-[#1B5E43] text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.walkin*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tambah pesanan walk-in
                        </a>

                        <!-- 4. Kelola Meja -->
                        <a href="{{ route('tables.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('tables.*') ? 'bg-[#1B5E43] text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('tables.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16M10 4v16"/></svg>
                            Kelola meja
                        </a>

                        <!-- 5. Pengaturan DP -->
                        <a href="{{ route('admin.settings') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.settings*') ? 'bg-[#1B5E43] text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.settings*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            Pengaturan DP
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Bottom Sidebar Area -->
            <div class="p-4 space-y-4">
                
                <!-- Status Klub Card -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-xs">
                    <div class="flex items-center gap-2 mb-2 font-bold text-white">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Klub sedang buka
                    </div>
                    <p class="text-gray-400 text-[11px] leading-tight">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}<br>
                        12.00 – 02.00 WIB
                    </p>
                    <div class="mt-3 pt-3 border-t border-white/10">
                        <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-400 hover:text-emerald-300 font-semibold transition">
                            Lihat website 
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Admin Profile & Logout -->
                <div class="pt-2 border-t border-white/10 flex items-center justify-between text-xs">
                    <div>
                        <p class="font-bold text-white truncate max-w-[130px]">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-gray-400 text-[10px]">Administrator</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-400 transition" title="Keluar dari akun">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- Top Navbar Bar -->
            <header class="bg-white border-b border-gray-100 h-16 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30">
                <div class="text-xs font-semibold text-gray-500">
                    @yield('breadcrumb', 'Operasional / Dashboard')
                </div>

                <div class="flex items-center gap-5">
                    <span class="text-xs font-semibold text-gray-600 hidden sm:inline">
                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </span>

                    <button class="relative text-gray-400 hover:text-gray-600 transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-[#1B5E43]"></span>
                    </button>

                    <div class="w-8 h-8 rounded-full bg-[#E8F3EE] text-[#1B5E43] font-bold text-xs flex items-center justify-center">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <main class="p-6 sm:p-8 flex-1">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>

        </div>

    </div>

</body>
</html>