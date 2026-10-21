<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking — Rackly Billiard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAF9] text-gray-800 antialiased min-h-screen flex flex-col justify-between" x-data="{ 
    filter: 'all',
    activeModal: null
}">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-10">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#1B5E43] text-white flex items-center justify-center font-bold text-xl shadow-sm">
                        8
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-gray-900 block leading-tight">RACKLY</span>
                        <span class="text-[10px] tracking-widest text-[#1B5E43] font-bold block uppercase -mt-1">Billiard Club</span>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
                    <a href="{{ url('/') }}" class="hover:text-[#1B5E43] transition">Beranda</a>
                    <a href="{{ url('/#tables') }}" class="hover:text-[#1B5E43] transition">Pilihan meja</a>
                    <a href="{{ url('/#how-to-book') }}" class="hover:text-[#1B5E43] transition">Cara booking</a>
                    <a href="{{ url('/#location') }}" class="hover:text-[#1B5E43] transition">Lokasi</a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('booking.history') }}" class="text-sm font-bold text-[#1B5E43]">
                    Riwayat booking
                </a>

                @auth
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 p-1 pl-2 pr-3 rounded-full hover:bg-gray-100 border border-transparent hover:border-gray-200 transition">
                            <div class="w-8 h-8 rounded-full bg-[#E8F3EE] text-[#1B5E43] font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-800 hidden sm:inline">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="userMenu" @click.outside="userMenu = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Masuk sebagai</p>
                                <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil saya</a>
                            <a href="{{ route('booking.history') }}" class="block px-4 py-2 text-sm font-semibold text-[#1B5E43] hover:bg-gray-50">Riwayat booking</a>
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
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-1">
        
        <!-- Flash Message -->
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

        <!-- Page Header & Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">AKUN SAYA / RIWAYAT BOOKING</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Setiap permainan, tercatat.</h1>
                <p class="text-gray-500 mt-2 text-sm sm:text-base">Lihat jadwal mendatang, lanjutkan pembayaran, atau booking lagi.</p>
            </div>

            <div>
                <a href="{{ url('/#tables') }}" class="inline-flex items-center gap-2 bg-[#1B5E43] hover:bg-[#154b35] text-white px-5 py-3 rounded-xl font-bold text-sm shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Booking baru
                </a>
            </div>
        </div>

        @php
            $countAll = $bookings->count();
            $countAktif = $bookings->whereIn('status', ['pending', 'dp_lunas'])->count();
            $countSelesai = $bookings->where('status', 'lunas')->count();
            $countBatal = $bookings->where('status', 'cancelled')->count();
        @endphp

        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <button @click="filter = 'all'" 
                        :class="filter === 'all' ? 'bg-[#1B5E43] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition">
                    Semua ({{ $countAll }})
                </button>
                <button @click="filter = 'aktif'" 
                        :class="filter === 'aktif' ? 'bg-[#1B5E43] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition">
                    Aktif ({{ $countAktif }})
                </button>
                <button @click="filter = 'selesai'" 
                        :class="filter === 'selesai' ? 'bg-[#1B5E43] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition">
                    Selesai ({{ $countSelesai }})
                </button>
                <button @click="filter = 'batal'" 
                        :class="filter === 'batal' ? 'bg-[#1B5E43] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition">
                    Dibatalkan ({{ $countBatal }})
                </button>
            </div>

            <span class="text-xs text-gray-400 font-medium hidden sm:inline">
                Urutan: Terbaru
            </span>
        </div>

        <!-- Bookings List -->
        <div class="space-y-4">
            @forelse($bookings as $booking)
                @php
                    $duration = \Carbon\Carbon::parse($booking->start_time)->diffInHours(\Carbon\Carbon::parse($booking->end_time));
                    $dpPercentage = $booking->total_price > 0 ? round(($booking->dp_amount / $booking->total_price) * 100) : 30;
                    $remaining = max(0, $booking->total_price - $booking->dp_amount);

                    // Category group for filter
                    $cat = 'other';
                    if (in_array($booking->status, ['pending', 'dp_lunas'])) {
                        $cat = 'aktif';
                    } elseif ($booking->status === 'lunas') {
                        $cat = 'selesai';
                    } elseif ($booking->status === 'cancelled') {
                        $cat = 'batal';
                    }
                @endphp

                <div x-show="filter === 'all' || filter === '{{ $cat }}'" 
                     class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden transition hover:border-gray-300">
                    
                    <!-- Top Sub-Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-gray-900">#CP-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-xs text-gray-400">• {{ $booking->is_walkin ? 'Walk-in Kasir' : 'Booking Online' }}</span>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($booking->status === 'dp_lunas')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Terkonfirmasi (DP Lunas)
                                </span>
                            @elseif($booking->status === 'lunas')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                    Selesai (Lunas)
                                </span>
                            @elseif($booking->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Pembayaran
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-800 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Main Card Body -->
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        
                        <!-- Left Details: Table & Schedule -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#E8F3EE] text-[#1B5E43] flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16M10 4v16"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">
                                    {{ $booking->billiardTable->table_number ?? 'Meja Telah Dihapus' }} · {{ $booking->billiardTable->type ?? 'Reguler' }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->translatedFormat('l, d M Y') }} · 
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H.i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H.i') }} WIB · 
                                    {{ $duration }} jam
                                </p>
                            </div>
                        </div>

                        <!-- Right Prices: Total & DP -->
                        <div class="flex items-center gap-8 text-right self-end md:self-center">
                            <div>
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total booking</p>
                                <p class="text-base sm:text-lg font-extrabold text-gray-900">
                                    Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">DP {{ $dpPercentage }}%</p>
                                <p class="text-base sm:text-lg font-extrabold text-[#1B5E43]">
                                    Rp{{ number_format($booking->dp_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Action & Info Row -->
                    <div class="px-6 py-4 bg-gray-50/70 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        
                        <div class="text-xs text-gray-500">
                            @if($booking->status === 'pending')
                                Bayar DP sebelum <strong class="text-gray-700">{{ \Carbon\Carbon::parse($booking->created_at)->addMinutes(15)->format('d M, H:i') }} WIB</strong>. Sisa Rp{{ number_format($remaining, 0, ',', '.') }} di lokasi.
                            @elseif($booking->status === 'dp_lunas')
                                DP Rp{{ number_format($booking->dp_amount, 0, ',', '.') }} sudah dibayar. Pelunasan sisa <strong class="text-gray-700">Rp{{ number_format($remaining, 0, ',', '.') }}</strong> dibayar di kasir saat datang.
                            @elseif($booking->status === 'lunas')
                                Pembayaran lunas. Terima kasih sudah bermain bersama kami!
                            @else
                                Pesanan ini telah dibatalkan.
                            @endif
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center">
                            
                            @if($booking->status === 'pending')
                                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 border border-gray-200 hover:border-red-300 hover:text-red-600 rounded-lg text-xs font-semibold text-gray-600 transition">
                                        Batalkan
                                    </button>
                                </form>
                                <a href="{{ route('booking.checkout', $booking->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-lg text-xs font-bold transition shadow-sm">
                                    Lanjutkan pembayaran &rarr;
                                </a>
                            @else
                                <button @click="activeModal = {{ $booking->id }}" class="px-3.5 py-1.5 border border-gray-200 bg-white hover:bg-gray-50 rounded-lg text-xs font-semibold text-gray-700 transition">
                                    Lihat detail
                                </button>
                                @if($booking->status === 'lunas')
                                    <a href="{{ url('/#tables') }}" class="px-3.5 py-1.5 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-lg text-xs font-bold transition">
                                        Booking lagi
                                    </a>
                                @endif
                            @endif

                        </div>

                    </div>

                    <!-- Modal Detail Pesanan -->
                    <div x-show="activeModal === {{ $booking->id }}" 
                         x-transition.opacity 
                         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" 
                         style="display: none;">
                        <div @click.outside="activeModal = null" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5">
                            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg">Rincian Booking #CP-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h3>
                                    <p class="text-xs text-gray-400">Dibuat pada {{ $booking->created_at->format('d M Y, H:i') }} WIB</p>
                                </div>
                                <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Meja</span>
                                    <span class="font-bold text-gray-900">{{ $booking->billiardTable->table_number ?? '-' }} · {{ $booking->billiardTable->type ?? 'Reguler' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Jadwal</span>
                                    <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($booking->start_time)->translatedFormat('d M Y') }}, {{ \Carbon\Carbon::parse($booking->start_time)->format('H.i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H.i') }} WIB</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Durasi</span>
                                    <span class="font-medium text-gray-800">{{ $duration }} jam</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Status</span>
                                    <span class="font-bold uppercase text-xs">{{ $booking->status }}</span>
                                </div>
                                <div class="border-t border-gray-100 pt-3 flex justify-between">
                                    <span class="text-gray-500">Total Harga</span>
                                    <span class="font-bold text-gray-900">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">DP Terbayar</span>
                                    <span class="font-bold text-emerald-700">Rp{{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Sisa Pelunasan di Lokasi</span>
                                    <span class="font-bold text-gray-900">Rp{{ number_format($remaining, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button @click="activeModal = null" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            @empty
                <div class="bg-white rounded-2xl border border-gray-200/80 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Belum ada riwayat booking</h3>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto mb-6">Pesan meja biliar favoritmu sekarang dan nikmati pengalaman bermain yang nyaman di Rackly Billiard Club.</p>
                    <a href="{{ url('/#tables') }}" class="inline-flex items-center gap-2 bg-[#1B5E43] hover:bg-[#154b35] text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition">
                        Pilih Meja Sekarang
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Info Card Bottom -->
        <div class="mt-8 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4 flex gap-3 text-xs text-emerald-900 leading-relaxed">
            <svg class="w-5 h-5 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                Pesanan yang belum dibayar dapat dibatalkan langsung. Untuk perubahan atau pembatalan pesanan terkonfirmasi, hubungi staf melalui WhatsApp <a href="https://wa.me/6281288881908" target="_blank" class="font-bold underline">0812-8888-1908</a>.
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500">
            <p>© {{ date('Y') }} Rackly Billiard Club. Hak cipta dilindungi.</p>
            <p>Butuh bantuan? WhatsApp <a href="https://wa.me/6281288881908" class="text-[#1B5E43] font-semibold hover:underline">0812-8888-1908</a></p>
        </div>
    </footer>

</body>
</html>