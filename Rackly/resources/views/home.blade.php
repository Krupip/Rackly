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
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-brand-green rounded-full flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="white" stroke-width="2.5">
                            <circle cx="12" cy="12" r="3"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>
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
                            <div class="w-8 h-8 bg-brand-green rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 nav-link">Masuk</a>
                        <a href="#pilih-meja" class="bg-brand-green text-white text-sm font-semibold px-4 py-2 rounded-lg hover-brand transition">Booking meja →</a>
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
                        <p class="text-2xl font-bold text-white">{{ $dpPercentage }}%</p>
                        <p class="text-gray-400 text-sm">DP, sisanya di lokasi</p>
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
                    <p class="text-brand-green font-semibold text-sm">Jl. Trunojoyo No. 28, Bandung → Sejak 2021</p>
                </div>
            </div>
        </div>
    </section>

    {{-- TABLE CATALOG --}}
    <section id="pilih-meja" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-green mb-2">Milih untuk setap permainan</p>
                    <h2 class="text-4xl font-black text-gray-900">Pilih sudut bermainmu.</h2>
                    <p class="text-gray-500 mt-2">Cek ketersediaan dan temukan meja yang pas untuk main ini.</p>
                </div>
                <a href="#pilih-meja" class="hidden md:block text-sm text-brand-green font-semibold underline underline-offset-2">Lihat semua meja →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($tables as $table)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
                        {{-- Table Image --}}
                        <div class="relative h-48 overflow-hidden bg-gray-800">
                            <img src="/images/Table photograph.png" alt="{{ $table->table_number }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90">
                            <div class="absolute top-3 left-3">
                                <span class="bg-green-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full">● Tersedia</span>
                            </div>
                            <div class="absolute bottom-3 right-3 bg-black/50 backdrop-blur-sm text-white text-xs px-2 py-1 rounded font-mono">
                                MEJA {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>

                        {{-- Table Info --}}
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-lg font-bold text-gray-900">{{ $table->table_number }}</h3>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-medium">{{ $table->type }}</span>
                            </div>
                            <p class="text-gray-500 text-sm mb-3">Area utama · 9 kaki · 2–4 pemain</p>
                            <p class="text-2xl font-black text-brand-green mb-4">
                                Rp {{ number_format($table->price_per_hour, 0, ',', '.') }}
                                <span class="text-sm text-gray-400 font-normal">/jam</span>
                            </p>

                            @auth
                                <a href="{{ route('bookings.create', $table->id) }}"
                                   class="block w-full text-center bg-brand-green hover-brand text-white font-semibold py-2.5 px-4 rounded-lg transition text-sm">
                                    Booking meja {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }} →
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="block w-full text-center bg-brand-green hover-brand text-white font-semibold py-2.5 px-4 rounded-lg transition text-sm">
                                    Masuk untuk booking →
                                </a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M12 8v8"/></svg>
                        </div>
                        <p class="text-gray-500 font-medium">Semua meja sedang tidak tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- 3-STEP GUIDE --}}
    <section id="cara-booking" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-green mb-2">Booking tanpa ribet</p>
                <h2 class="text-4xl font-black text-gray-900">Tiga langkah. Lalu, break pertama.</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-10">
                <div>
                    <div class="text-5xl font-black text-gray-100 mb-3">01</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Pilih meja & jadwal</h3>
                    <p class="text-gray-500 leading-relaxed">Tentukan meja, tanggal, dan durasi bermain. Cek slot yang tersedia secara langsung.</p>
                </div>
                <div>
                    <div class="text-5xl font-black text-gray-100 mb-3">02</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Amankan dengan DP {{ $dpPercentage }}%</h3>
                    <p class="text-gray-500 leading-relaxed">Masuk ke akunmu dan bayar DP melalui pilihan pembayaran Midtrans.</p>
                </div>
                <div>
                    <div class="text-5xl font-black text-gray-100 mb-3">03</div>
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
                                <p class="text-xs text-brand-green mt-1 font-medium">Klik untuk buka Google Maps →</p>
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
                Pilih meja & booking →
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
    </script>
</body>
</html>