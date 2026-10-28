<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rackly Billiard Club — Booking Meja Biliar</title>
    <meta name="description" content="Booking meja biliar favorit kamu dengan mudah. Bayar DP dan main langsung.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background-image: linear-gradient(to right, rgba(0,0,0,0.85) 40%, rgba(0,0,0,0.4) 100%), url('/images/Atmospheric club.png');
            background-size: cover;
            background-position: center;
        }
        .club-img-bg {
            background-image: url('/images/Club atmosphere.png');
            background-size: cover;
            background-position: center;
        }
        .brand-green { color: #1a5c38; }
        .bg-brand-green { background-color: #1a5c38; }
        .bg-brand-green-dark { background-color: #14422a; }
        .bg-brand-green-light { background-color: #e8f5ee; }
        .border-brand-green { border-color: #1a5c38; }
        .hover-brand:hover { background-color: #14422a; }
        .table-card:hover .table-overlay { opacity: 1; }
        .table-overlay { opacity: 0; transition: opacity 0.3s; }
        .nav-link { transition: color 0.2s; }
        .nav-link:hover { color: #1a5c38; }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .faq-answer.open { max-height: 200px; }
        .custom-table-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-table-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }
        .custom-table-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-table-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #1a5c38;
        }
        .custom-table-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-full bg-[#1B5E43] text-white flex items-center justify-center font-bold text-xl shadow-sm">
                        8
                    </div>
                    <div class="leading-tight">
                        <div class="font-bold text-gray-900 text-sm tracking-wide uppercase">Rackly</div>
                        <div class="text-xs text-gray-500 -mt-0.5">Billiard Club</div>
                    </div>
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center gap-7">
                    <a href="{{ route('dashboard') }}" class="nav-link text-sm font-medium text-gray-700">Beranda</a>
                    <a href="#pilih-meja" class="nav-link text-sm font-medium text-gray-700">Pilihan meja</a>
                    <a href="#cara-booking" class="nav-link text-sm font-medium text-gray-700">Cara booking</a>
                    <a href="#lokasi" class="nav-link text-sm font-medium text-gray-700">Lokasi</a>
                </div>

                {{-- Auth --}}
                <div class="flex items-center gap-3">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-600 nav-link">Panel Admin</a>
                        @else
                            <a href="{{ route('booking.history') }}" class="text-sm font-medium text-gray-600 nav-link">Riwayat booking</a>
                            @auth
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center gap-2 p-1 pl-2 pr-3 rounded-full hover:bg-gray-100 border border-transparent hover:border-gray-200 transition">
                                        <div class="w-8 h-8 rounded-full bg-[#E8F3EE] text-[#1B5E43] font-bold text-xs flex items-center justify-center">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800 hidden sm:inline">{{ Auth::user()->name }}</span>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50">
                                        <div class="px-4 py-2 border-b border-gray-100">
                                            <p class="text-xs text-gray-500">Masuk sebagai</p>
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-semibold text-[#1B5E43] hover:bg-gray-50">Profil saya</a>
                                        <a href="{{ route('booking.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Riwayat booking</a>
                                        @if(Auth::user()->is_admin)
                                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-[#1B5E43] font-semibold hover:bg-emerald-50">Admin Panel</a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                                        </form>
                                    </div>
                                </div>
                            @endauth
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 nav-link">Masuk</a>
                        <a href="#pilih-meja" class="bg-brand-green text-white text-sm font-semibold px-4 py-2 rounded-lg hover-brand transition">Booking meja </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="hero-bg min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full">
            <div class="max-w-xl">
                <p class="text-green-400 text-xs font-semibold uppercase tracking-widest mb-4">Ruang Bermain Andalan di Kota</p>
                <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-5">
                    Satu meja.<br>Banyak cerita.
                </h1>
                <p class="text-gray-300 text-lg mb-8 leading-relaxed">
                    Tempat pukulan terbaik dan obrolan panjang bertemu.<br>
                    Pilih meja favoritmu, kunci jadwal, dan nikmati permainannya.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="#pilih-meja" class="bg-brand-green text-white font-semibold px-6 py-3 rounded-lg hover-brand transition flex items-center gap-2">
                        Booking meja sekarang
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="#cara-booking" class="border border-white/30 text-white font-semibold px-6 py-3 rounded-lg hover:bg-white/10 transition">
                        Jelajahi klub ↓
                    </a>
                </div>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-8 mt-12 pt-8 border-t border-white/20">
                    <div>
                        @if($dpType === 'fixed')
                        <p class="text-2xl font-bold text-white">Rp{{ number_format($dpNominal, 0, ',', '.') }}</p>
                        <p class="text-gray-400 text-sm">DP nominal tetap, sisanya di lokasi</p>
                    @else
                        <p class="text-2xl font-bold text-white">{{ $dpPercentage }}%</p>
                        <p class="text-gray-400 text-sm">DP, sisanya di lokasi</p>
                    @endif
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $openHour }}–{{ $closeHour }}</p>
                        <p class="text-gray-400 text-sm">Waktu untuk bermain</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- WELCOME SECTION --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-green mb-3">Selamat datang di Rackly</p>
                    <h2 class="text-4xl font-black text-gray-900 leading-tight mb-5">
                        Bukan sekadar billiard.<br>Ini sudut favoritmu.
                    </h2>
                </div>
                <div>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Dari latihan sampai sampai pertandingan serius, kami menyiapkan ruang untuk semua level pemain. Meja terawat, pencahayaan presisi, dan suasana hangat — agar kamu bisa fokus pada setiap pukulan.
                    </p>
                    <p class="text-brand-green font-semibold text-sm">Jl. Trunojoyo No. 28, Bandung  Sejak 2021</p>
                </div>
            </div>
        </div>
    </section>

    {{-- TABLE CATALOG --}}
    <section id="pilih-meja" class="py-20 bg-gray-50"
             x-data="homeTableCatalog(
                 {{ json_encode($tables->map(function($t, $idx) {
                     return [
                         'id' => $t->id,
                         'table_number' => $t->table_number,
                         'type' => $t->type,
                         'index_label' => str_pad($idx + 1, 2, '0', STR_PAD_LEFT),
                         'price_per_hour' => (int) $t->price_per_hour,
                         'formatted_price' => 'Rp ' . number_format($t->price_per_hour, 0, ',', '.'),
                         'is_occupied' => false,
                         'booking_url' => route('bookings.create', $t->id),
                     ];
                 })) }},
                 {{ Auth::check() ? 'true' : 'false' }},
                 {{ json_encode($existingBookings ?? []) }},
                 '{{ $openHour ?? '10:00' }}',
                 '{{ $closeHour ?? '23:00' }}'
             )"
             x-init="initRealtime()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-brand-green">Pilihan untuk setiap permainan</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                            Live Updates
                        </span>
                    </div>
                    <h2 class="text-4xl font-black text-gray-900">Pilih sudut bermainmu.</h2>
                    <p class="text-gray-500 mt-2">Cek ketersediaan dan temukan meja yang pas untuk main hari ini.</p>
                </div>
            </div>

            <!-- Filter Ketersediaan Meja Bar -->
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-200/80 shadow-sm mb-10 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#1B5E43] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900">Filter Jadwal Bermain</h3>
                            <p class="text-xs text-gray-400">Pilih tanggal, jam mulai, dan jam selesai untuk melihat meja yang tersedia.</p>
                        </div>
                    </div>

                    <template x-if="isFilterActive || filterDate || filterStartTime || filterEndTime">
                        <button type="button" @click="resetFilter()"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-red-600 transition self-start sm:self-auto py-1.5 px-3 rounded-lg hover:bg-red-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset Filter
                        </button>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- 1. Tanggal Main -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Tanggal Main
                            </label>
                            <div class="flex items-center gap-1.5 text-[11px]">
                                <button type="button" @click="setQuickDate(0)"
                                        class="text-gray-500 hover:text-[#1B5E43] font-medium underline underline-offset-2">
                                    Hari Ini
                                </button>
                                <span class="text-gray-300">·</span>
                                <button type="button" @click="setQuickDate(1)"
                                        class="text-gray-500 hover:text-[#1B5E43] font-medium underline underline-offset-2">
                                    Besok
                                </button>
                            </div>
                        </div>
                        <input type="date" x-model="filterDate" min="{{ date('Y-m-d') }}" @change="onDateChange()"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                    </div>

                    <!-- 2. Jam Mulai -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Jam Mulai
                        </label>
                        <div class="relative">
                            <button type="button" @click="if(filterDate) { openStart = !openStart; openEnd = false; } else { showAlert('Pilih Tanggal Main', 'Silakan pilih tanggal main terlebih dahulu sebelum menentukan jam mulai.'); }"
                                    :class="{ 'opacity-60': !filterDate }"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 flex justify-between items-center text-left focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <span x-text="filterStartTime ? filterStartTime + ' WIB' : '-- Pilih Jam Mulai --'"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openStart" @click.outside="openStart = false"
                                 class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-56 overflow-y-auto py-1 text-xs">
                                <template x-for="item in availableStartHours" :key="item">
                                    <button type="button"
                                            @click="if(!isPastHour(item)) { selectStartTime(item); }"
                                            :disabled="isPastHour(item)"
                                            class="w-full px-4 py-2 text-left transition flex items-center justify-between font-semibold"
                                            :class="{
                                                'bg-emerald-50 text-[#1B5E43] font-bold': filterStartTime === item,
                                                'opacity-40 bg-gray-50 text-gray-400 cursor-not-allowed': isPastHour(item),
                                                'text-gray-700 hover:bg-[#1B5E43] hover:text-white': !isPastHour(item) && filterStartTime !== item
                                            }">
                                        <span x-text="item + ' WIB'"></span>
                                        <span x-show="isPastHour(item)" class="text-[10px] text-gray-400 font-normal italic">(Sudah lewat)</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Jam Selesai -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Jam Selesai
                        </label>
                        <div class="relative">
                            <button type="button" @click="if(!filterDate) { showAlert('Pilih Tanggal Main', 'Silakan pilih tanggal main terlebih dahulu sebelum menentukan jam selesai.'); } else if(!filterStartTime) { showAlert('Pilih Jam Mulai', 'Silakan tentukan jam mulai terlebih dahulu sebelum memilih jam selesai.'); } else { openEnd = !openEnd; openStart = false; }"
                                    :class="{ 'opacity-60': !filterStartTime }"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 flex justify-between items-center text-left focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <span x-text="filterEndTime ? filterEndTime + ' WIB' : (filterStartTime ? '-- Pilih Jam Selesai --' : 'Pilih Jam Mulai Dulu')"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openEnd && filterStartTime" @click.outside="openEnd = false"
                                 class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-56 overflow-y-auto py-1 text-xs">
                                <template x-for="item in availableEndHours" :key="item">
                                    <button type="button"
                                            @click="if(!isPastHour(item, true)) { selectEndTime(item); }"
                                            :disabled="isPastHour(item, true)"
                                            class="w-full px-4 py-2 text-left transition flex items-center justify-between font-semibold"
                                            :class="{
                                                'bg-emerald-50 text-[#1B5E43] font-bold': filterEndTime === item,
                                                'opacity-40 bg-gray-50 text-gray-400 cursor-not-allowed': isPastHour(item, true),
                                                'text-gray-700 hover:bg-[#1B5E43] hover:text-white': !isPastHour(item, true) && filterEndTime !== item
                                            }">
                                        <span x-text="item + ' WIB'"></span>
                                        <span x-show="isPastHour(item, true)" class="text-[10px] text-gray-400 font-normal italic">(Sudah lewat)</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Filter Active Banner -->
                <template x-if="isFilterActive">
                    <div class="bg-emerald-50/90 border border-emerald-200 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-emerald-950">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span>
                            <span>
                                Filter aktif: Menampilkan <strong class="text-emerald-900" x-text="filteredTables.length"></strong> meja yang tersedia untuk tanggal <strong class="text-emerald-900" x-text="formatDateIndo(filterDate)"></strong> pukul <strong class="text-emerald-900" x-text="filterStartTime + ' – ' + filterEndTime + ' WIB'"></strong>.
                            </span>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-700 shrink-0 bg-white/80 border border-emerald-200/80 px-2.5 py-1 rounded-full">
                            Hanya Meja Tersedia
                        </span>
                    </div>
                </template>
            </div>

            <!-- Indikator Gulir / Scroll jika meja > 6 -->
            <div x-show="filteredTables.length > 6" class="flex items-center justify-between text-xs text-gray-500 mb-3 px-1">
                <span class="text-[11px] sm:text-xs text-gray-500 font-medium">
                    Menampilkan <strong class="text-gray-900" x-text="filteredTables.length"></strong> meja
                </span>
                <span class="inline-flex items-center gap-1.5 text-[11px] sm:text-xs text-[#1B5E43] font-semibold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60">
                    <svg class="w-3.5 h-3.5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    Gulir ke bawah untuk meja lainnya
                </span>
            </div>

            <!-- Scrollable Container Meja jika Meja > 6 -->
            <div :class="filteredTables.length > 6 ? 'max-h-[540px] sm:max-h-[580px] lg:max-h-[620px] overflow-y-auto pr-1 sm:pr-2.5 pb-2 -mr-1 sm:-mr-2.5 custom-table-scrollbar' : ''">
                <!-- Grid Meja: 2 kolom di HP, 3 kolom di Desktop -->
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
                    <template x-for="table in filteredTables" :key="table.id">
                        <div class="bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition-all group flex flex-col justify-between">
                            {{-- Table Image --}}
                            <div class="relative h-32 sm:h-48 overflow-hidden bg-gray-800">
                                <img src="/images/Table photograph.png" :alt="table.table_number"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90">
                                
                                <div class="absolute top-2 left-2 sm:top-3 sm:left-3">
                                    <template x-if="isFilterActive">
                                        <span class="bg-emerald-600 text-white text-[9px] sm:text-xs font-semibold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow-sm flex items-center gap-1 sm:gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            <span class="hidden sm:inline">Tersedia di Jam Ini</span>
                                            <span class="sm:hidden">Tersedia</span>
                                        </span>
                                    </template>
                                    <template x-if="!isFilterActive">
                                        <div>
                                            <template x-if="!table.is_occupied">
                                                <span class="bg-green-500 text-white text-[9px] sm:text-xs font-semibold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow-sm">● Tersedia</span>
                                            </template>
                                            <template x-if="table.is_occupied">
                                                <span class="bg-amber-500 text-white text-[9px] sm:text-xs font-semibold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow-sm">
                                                    <span class="hidden sm:inline">● Sedang Digunakan</span>
                                                    <span class="sm:hidden">● Dipakai</span>
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="absolute bottom-2 right-2 sm:bottom-3 sm:right-3 bg-black/60 backdrop-blur-sm text-white text-[9px] sm:text-xs px-1.5 py-0.5 sm:px-2 sm:py-1 rounded font-mono"
                                     x-text="'MEJA ' + table.index_label">
                                </div>
                            </div>

                            {{-- Table Info --}}
                            <div class="p-3 sm:p-5 flex flex-col justify-between flex-1">
                                <div>
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-1 gap-0.5 sm:gap-1">
                                        <h3 class="text-sm sm:text-lg font-bold text-gray-900 leading-tight truncate" x-text="table.table_number"></h3>
                                        <span class="text-[9px] sm:text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-medium self-start sm:self-auto shrink-0" x-text="table.type"></span>
                                    </div>
                                    <p class="text-gray-500 text-[11px] sm:text-sm mb-2 sm:mb-3 truncate">Area utama · 9 kaki · 2–4 org</p>
                                    <p class="text-base sm:text-2xl font-black text-brand-green mb-2.5 sm:mb-4">
                                        <span x-text="table.formatted_price"></span>
                                        <span class="text-[10px] sm:text-sm text-gray-400 font-normal">/jam</span>
                                    </p>
                                </div>

                                <template x-if="isAuth">
                                    <a :href="getBookingUrl(table)"
                                       class="block w-full text-center bg-brand-green hover-brand text-white font-semibold py-2 sm:py-2.5 px-2 sm:px-4 rounded-lg transition text-xs sm:text-sm truncate">
                                        Booking meja <span x-text="table.index_label"></span> 
                                    </a>
                                </template>
                                <template x-if="!isAuth">
                                    <a href="{{ route('login') }}"
                                       class="block w-full text-center bg-brand-green hover-brand text-white font-semibold py-2 sm:py-2.5 px-2 sm:px-4 rounded-lg transition text-xs sm:text-sm truncate">
                                        Masuk untuk booking 
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="filteredTables.length === 0">
                        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300 p-8">
                            <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <template x-if="isFilterActive">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900 mb-1">Tidak Ada Meja yang Tersedia</h4>
                                    <p class="text-gray-500 text-sm max-w-md mx-auto mb-5">
                                        Seluruh meja sudah dibooking pada jam <strong x-text="filterStartTime + ' – ' + filterEndTime + ' WIB'"></strong> tanggal <strong x-text="formatDateIndo(filterDate)"></strong>. Silakan coba pilih jam atau tanggal lain.
                                    </p>
                                    <button type="button" @click="resetFilter()"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-green hover-brand text-white rounded-xl text-xs font-bold transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Tampilkan Semua Meja (Reset Filter)
                                    </button>
                                </div>
                            </template>
                            <template x-if="!isFilterActive">
                                <p class="text-gray-500 font-medium">Semua meja sedang tidak tersedia saat ini.</p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Custom Alert Popup Modal -->
        <div x-show="alertModal.show" 
             x-transition.opacity 
             class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" 
             style="display: none;">
            <div @click.outside="closeAlert()" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center transform transition-all">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto border border-amber-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-extrabold text-gray-900" x-text="alertModal.title"></h3>
                    <p class="text-xs text-gray-500 leading-relaxed" x-text="alertModal.message"></p>
                </div>
                <button type="button" @click="closeAlert()" class="w-full py-2.5 px-4 bg-[#1B5E43] hover:bg-[#154b35] text-white font-bold rounded-xl text-xs transition shadow-sm">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </section>

    {{-- 3-STEP GUIDE --}}
    <section id="cara-booking" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-green mb-2">Booking tanpa ribet</p>
                <h2 class="text-4xl font-black text-gray-900">Tiga langkah. Lalu, break pertama.</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-10">
                <div>
                    <div class="text-5xl font-black text-[#1A5C38] mb-3">01</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Pilih meja & jadwal</h3>
                    <p class="text-gray-500 leading-relaxed">Tentukan meja, tanggal, dan durasi bermain. Cek slot yang tersedia secara langsung.</p>
                </div>
                <div>
                    <div class="text-5xl font-black text-[#1A5C38] mb-3">02</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        @if($dpType === 'fixed')
                            Amankan dengan DP Rp{{ number_format($dpNominal, 0, ',', '.') }}
                        @else
                            Amankan dengan DP {{ $dpPercentage }}%
                        @endif
                    </h3>
                    <p class="text-gray-500 leading-relaxed">Masuk ke akunmu dan bayar DP melalui pilihan pembayaran Midtrans.</p>
                </div>
                <div>
                    <div class="text-5xl font-black text-[#1A5C38] mb-3">03</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Datang & mulai bermain</h3>
                    <p class="text-gray-500 leading-relaxed">Tunjukan kode booking di kasir, lunasi sisanya, dan nikmati waktu bermainmu.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div class="club-img-bg rounded-2xl h-80 md:h-96"></div>
                <div class="pl-0 md:pl-8">
                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-green mb-2">Lebih dari meja yang bagus</p>
                    <h2 class="text-4xl font-black text-gray-900 mb-5">Nyaman bermain.<br>Betah berlama-lama.</h2>
                    <p class="text-gray-500 mb-8">Semua yang kamu butuhkan untuk satu sesi yang sempurna, dari pencahayaan hingga teman ngobrol.</p>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="w-8 h-8 bg-brand-green-light rounded-lg flex items-center justify-center mb-2">
                                <svg width="16" height="16" fill="none" stroke="#1a5c38" stroke-width="2" viewBox="0 0 24 24"><path d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4 4 0 003 15z"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Ruangan ber-AC</h4>
                            <p class="text-gray-500 text-xs">Sejuk di setiap sesi</p>
                        </div>
                        <div>
                            <div class="w-8 h-8 bg-brand-green-light rounded-lg flex items-center justify-center mb-2">
                                <svg width="16" height="16" fill="none" stroke="#1a5c38" stroke-width="2" viewBox="0 0 24 24"><path d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Wi-Fi cepat</h4>
                            <p class="text-gray-500 text-xs">Tanpa batas</p>
                        </div>
                        <div>
                            <div class="w-8 h-8 bg-brand-green-light rounded-lg flex items-center justify-center mb-2">
                                <svg width="16" height="16" fill="none" stroke="#1a5c38" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Kafe & camilan</h4>
                            <p class="text-gray-500 text-xs">Tersedia di tiap permainan</p>
                        </div>
                        <div>
                            <div class="w-8 h-8 bg-brand-green-light rounded-lg flex items-center justify-center mb-2">
                                <svg width="16" height="16" fill="none" stroke="#1a5c38" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17l3-3 3 3M9 7l3 3 3-3"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Parkir tersedia</h4>
                            <p class="text-gray-500 text-xs">Datang tanpa khawatir</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- LOCATION SECTION --}}
    <section id="lokasi" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-green mb-3">Mampir ke sudut kami</p>
                    <h2 class="text-4xl font-black text-gray-900 mb-4">Di jantung kota.<br>Jauh dari rutinitas.</h2>
                    <p class="text-gray-500 mb-1">Jl. Trunojoyo No. 28, Citarum, Bandung Wetan,</p>
                    <p class="text-gray-500 mb-6">Kota Bandung, Jawa Barat 40115.</p>
                    <div class="flex gap-8 text-sm text-gray-700 mb-8">
                        <div>
                            <p class="font-medium">Senin – Minggu</p>
                            <p class="text-brand-green font-bold">{{ $openHour }} – {{ $closeHour }} WIB</p>
                        </div>
                    </div>
                    <a href="https://maps.google.com/?q=Jl.+Trunojoyo+No.+28+Bandung" target="_blank"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-brand-green hover:underline underline-offset-2">
                        Buka di Google Maps
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>
                    </a>
                </div>
                {{-- Static Map --}}
                <a href="https://maps.google.com/?q=Jl.+Trunojoyo+No.+28+Bandung" target="_blank" class="block">
                    <div class="rounded-2xl overflow-hidden bg-gray-200 h-72 relative shadow-md hover:shadow-lg transition-shadow">
                        <img src="https://maps.googleapis.com/maps/api/staticmap?center=Jl.+Trunojoyo+No.+28+Bandung&zoom=15&size=600x300&markers=color:green%7CJl.+Trunojoyo+No.+28+Bandung&key=YOUR_API_KEY_PLACEHOLDER"
                             alt="Peta lokasi Rackly Billiard"
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'">
                        {{-- Fallback jika API key tidak ada --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 bg-brand-green rounded-full flex items-center justify-center shadow-lg">
                                <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-gray-800">Rackly Billiard Club</p>
                                <p class="text-sm text-gray-500">Jl. Trunojoyo No. 28, Bandung</p>
                                <p class="text-xs text-brand-green mt-1 font-medium">Klik untuk buka Google Maps </p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- FAQ SECTION --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-green mb-3">Sebelum mulai bermain</p>
                    <h2 class="text-4xl font-black text-gray-900">Ada yang ingin ditanyakan?</h2>
                    <p class="text-gray-500 mt-4 text-sm">Tim kami siap membantu setiap hari.<br>WhatsApp: 0812 8888 1908</p>
                </div>
                <div class="space-y-1" x-data="{ open: null }">
                    @php
                        $faqs = [
                            ['q' => 'Berapa minimum durasi booking?', 'a' => 'Minimum 1 jam, dengan kelipatan 1 jam. Harga Reguler Rp80.000/jam dan VIP Rp120.000/jam.'],
                            ['q' => 'Bagaimana aturan pembayaran?', 'a' => 'Bayar DP terlebih dahulu secara online. Untuk jadwal yang sudah terkonfirmasi, hubungi staf maksimal 3 jam sebelumnya untuk perubahan.'],
                            ['q' => 'Apakah harus membawa stik sendiri?', 'a' => 'Tidak. Stik dan semua peralatan sudah kami sediakan. Kamu cukup datang dan membawa rasa ingin bermain.'],
                            ['q' => 'Apa yang terjadi jika DP tidak dibayar?', 'a' => 'Pesanan akan kedaluwarsa otomatis dalam 15 menit setelah dibuat, dan slot meja akan dilepas kembali.'],
                        ];
                    @endphp
                    @foreach($faqs as $i => $faq)
                        <div class="border-b border-gray-200 py-4">
                            <button class="w-full flex justify-between items-center text-left" @click="open = open === {{ $i }} ? null : {{ $i }}">
                                <span class="font-semibold text-gray-900 text-sm">{{ $faq['q'] }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0 ml-4" :class="open === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open === {{ $i }}" x-collapse style="display:none" class="mt-2 text-sm text-gray-500 leading-relaxed">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <section class="bg-brand-green py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h2 class="text-3xl font-black text-white">Meja berikutnya, untuk kamu.</h2>
                <p class="text-green-200 mt-1">Apa tuntamu. Kami siapkan tempat dan permainannya.</p>
            </div>
            <a href="#pilih-meja" class="bg-white text-brand-green font-bold px-8 py-3 rounded-lg hover:bg-gray-100 transition whitespace-nowrap">
                Pilih meja & booking 
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-gray-400 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-10 mb-10">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 bg-brand-green rounded-full flex items-center justify-center">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="9"/></svg>
                        </div>
                        <span class="font-bold text-white text-sm">RACKLY</span>
                    </div>
                    <p class="text-xs leading-relaxed">Permainan. Rileks. Komunitas.<br>Jl. Trunojoyo No. 28, Bandung.</p>
                </div>
                <div>
                    <h5 class="text-white font-semibold text-sm mb-3">Jelajahi klub</h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#pilih-meja" class="hover:text-white transition">Pilihan meja</a></li>
                        <li><a href="#cara-booking" class="hover:text-white transition">Cara booking</a></li>
                        @auth
                        <li><a href="{{ route('booking.history') }}" class="hover:text-white transition">Riwayat booking</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h5 class="text-white font-semibold text-sm mb-3">Tetap terhubung</h5>
                    <ul class="space-y-2 text-xs">
                        <li>WhatsApp: 0812-8888-1908</li>
                        <li>Email: hello@rackly.id</li>
                        <li>{{ $openHour }}–{{ $closeHour }} WIB, Setiap hari</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center gap-2">
                <p class="text-xs">© 2026 Rackly Billiard · Bandung</p>
                <div class="flex gap-4 text-xs">
                    <a href="#" class="hover:text-white transition">Syarat & ketentuan</a>
                    <a href="#" class="hover:text-white transition">Kebijakan privasi</a>
                    @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-white transition">Keluar</button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Real-time Home Table Catalog with Date/Time Availability Filter
        function homeTableCatalog(initialTables, isAuth, initialBookings, openHour, closeHour) {
            return {
                tables: initialTables || [],
                isAuth: !!isAuth,
                existingBookings: initialBookings || [],
                openHour: openHour || '10:00',
                closeHour: closeHour || '23:00',
                filterDate: '',
                filterStartTime: '',
                filterEndTime: '',
                openStart: false,
                openEnd: false,
                alertModal: {
                    show: false,
                    title: '',
                    message: ''
                },
                pollingTimer: null,

                showAlert(title, message) {
                    this.alertModal = {
                        show: true,
                        title: title || 'Pemberitahuan',
                        message: message || ''
                    };
                },

                closeAlert() {
                    this.alertModal.show = false;
                },

                initRealtime() {
                    // Poll data setiap 4 detik untuk update ketersediaan real-time
                    this.pollingTimer = setInterval(() => {
                        this.fetchTables();
                    }, 4000);
                },

                async fetchTables() {
                    try {
                        const res = await fetch('{{ route('realtime.tables') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            if (data && data.tables) {
                                this.tables = data.tables;
                            }
                            if (data && data.existingBookings) {
                                this.existingBookings = data.existingBookings;
                            }
                        }
                    } catch (err) {
                        // Silent catch on network blip
                    }
                },

                setQuickDate(offsetDays) {
                    const d = new Date();
                    d.setDate(d.getDate() + offsetDays);
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    this.filterDate = `${yyyy}-${mm}-${dd}`;
                    this.onDateChange();
                },

                parseHour(timeStr, isEnd = false) {
                    if (!timeStr) return 0;
                    let h = parseInt(timeStr.split(':')[0], 10);
                    if (isEnd && h === 0) h = 24;
                    return h;
                },

                isPastHour(timeStr, isEnd = false) {
                    if (!timeStr || !this.filterDate) return false;
                    const todayStr = '{{ date('Y-m-d') }}';
                    if (this.filterDate < todayStr) return true;
                    if (this.filterDate > todayStr) return false;

                    let hour = parseInt(timeStr.split(':')[0], 10);
                    if (isEnd && hour === 0) hour = 24;
                    const nowH = new Date().getHours();
                    return hour <= nowH;
                },

                get availableStartHours() {
                    let openH = parseInt(this.openHour.split(':')[0], 10);
                    let closeH = parseInt(this.closeHour.split(':')[0], 10);
                    if (closeH <= openH && closeH > 0) closeH += 24;
                    else if (closeH === 0) closeH = 24;

                    let hours = [];
                    for (let i = openH; i < closeH; i++) {
                        hours.push((i % 24).toString().padStart(2, '0') + ':00');
                    }
                    return hours;
                },

                get availableEndHours() {
                    if (!this.filterStartTime) return [];
                    let start = parseInt(this.filterStartTime.split(':')[0], 10);
                    let openH = parseInt(this.openHour.split(':')[0], 10);
                    let closeH = parseInt(this.closeHour.split(':')[0], 10);
                    if (closeH <= openH && closeH > 0) closeH += 24;
                    else if (closeH === 0) closeH = 24;

                    let hours = [];
                    for (let i = start + 1; i <= closeH; i++) {
                        hours.push((i === 24 ? '00:00' : (i % 24).toString().padStart(2, '0') + ':00'));
                    }
                    return hours;
                },

                selectStartTime(item) {
                    this.filterStartTime = item;
                    this.openStart = false;
                    let startH = parseInt(item.split(':')[0], 10);
                    let nextH = startH + 1;
                    let openH = parseInt(this.openHour.split(':')[0], 10);
                    let closeH = parseInt(this.closeHour.split(':')[0], 10);
                    if (closeH <= openH && closeH > 0) closeH += 24;
                    else if (closeH === 0) closeH = 24;

                    if (!this.filterEndTime || this.parseHour(this.filterEndTime, true) <= startH) {
                        if (nextH <= closeH) {
                            this.filterEndTime = (nextH === 24 ? '00:00' : (nextH % 24).toString().padStart(2, '0') + ':00');
                        }
                    }
                },

                selectEndTime(item) {
                    this.filterEndTime = item;
                    this.openEnd = false;
                },

                onDateChange() {
                    if (this.filterStartTime && this.isPastHour(this.filterStartTime)) {
                        this.filterStartTime = '';
                        this.filterEndTime = '';
                    }
                },

                resetFilter() {
                    this.filterDate = '';
                    this.filterStartTime = '';
                    this.filterEndTime = '';
                    this.openStart = false;
                    this.openEnd = false;
                },

                get isFilterActive() {
                    return !!(this.filterDate && this.filterStartTime && this.filterEndTime);
                },

                isTableConflict(tableId) {
                    if (!this.isFilterActive) return false;

                    const sH = this.parseHour(this.filterStartTime, false);
                    const eH = this.parseHour(this.filterEndTime, true);
                    if (eH <= sH) return false;

                    return this.existingBookings.some(b => {
                        if (parseInt(b.table_id) !== parseInt(tableId)) return false;
                        if (b.date !== this.filterDate) return false;

                        const bStart = this.parseHour(b.start, false);
                        const bEnd   = this.parseHour(b.end, true);

                        return sH < bEnd && eH > bStart;
                    });
                },

                isTableAvailable(tableId) {
                    return !this.isTableConflict(tableId);
                },

                get filteredTables() {
                    if (!this.isFilterActive) {
                        return this.tables;
                    }
                    // Ketika filter aktif, tampilkan meja yang tersedia saja
                    return this.tables.filter(table => this.isTableAvailable(table.id));
                },

                getBookingUrl(table) {
                    if (this.isFilterActive) {
                        return table.booking_url + '?date=' + encodeURIComponent(this.filterDate) + '&start=' + encodeURIComponent(this.filterStartTime) + '&end=' + encodeURIComponent(this.filterEndTime);
                    }
                    return table.booking_url;
                },

                formatDateIndo(dateStr) {
                    if (!dateStr) return '';
                    try {
                        const parts = dateStr.split('-');
                        if (parts.length === 3) {
                            const d = new Date(parts[0], parts[1] - 1, parts[2]);
                            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                        }
                    } catch (e) {}
                    return dateStr;
                }
            };
        }
    </script>
</body>
</html>