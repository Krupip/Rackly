<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran DP #{{ $booking->id }} — Rackly Billiard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAF9] text-gray-800 antialiased min-h-screen flex flex-col justify-between">

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
                <a href="{{ route('booking.history') }}" class="hidden sm:inline-block text-sm font-medium text-gray-700 hover:text-[#1B5E43] transition">
                    Riwayat booking
                </a>

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
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil saya</a>
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
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-1">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-8">
            <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">BOOKING / PEMBAYARAN DP</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Selangkah lagi menuju meja kamu.</h1>
            <p class="text-gray-500 mt-2 text-sm sm:text-base">Bayar DP untuk mengonfirmasi jadwal. Sisa pembayaran dilunasi di klub.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Card: Pembayaran (8 cols) -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                    
                    <!-- Card Top Header -->
                    <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-0.5">PEMBAYARAN UNTUK</span>
                            <h2 class="text-xl font-bold text-gray-900">Rackly Billiard Club</h2>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200/60">
                            <span class="text-xs font-semibold text-gray-500">Powered by</span>
                            <span class="text-sm font-black tracking-tight text-blue-900 flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                midtrans
                            </span>
                        </div>
                    </div>

                    <!-- Payment Details Body -->
                    <div class="p-6 sm:p-8 space-y-6">
                        
                        <!-- Total DP & Status -->
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total pembayaran DP</p>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <span class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                                    Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu pembayaran
                                </span>
                            </div>
                        </div>

                        @php
                            $expiryMinutes = (int) \App\Models\Setting::get('dp_expiry_minutes', 15);
                        @endphp
                        <!-- Timer Box -->
                        <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-4 sm:p-5 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-amber-900">
                                    Selesaikan sebelum {{ \Carbon\Carbon::parse($booking->created_at)->addMinutes($expiryMinutes)->format('d M Y, H:i') }} WIB
                                </p>
                                <p class="text-xs text-amber-700 mt-0.5">Jadwal dilepas otomatis jika pembayaran kedaluwarsa.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span id="countdown" class="text-2xl font-bold font-mono text-amber-900">{{ str_pad($expiryMinutes, 2, '0', STR_PAD_LEFT) }}:00</span>
                            </div>
                        </div>

                        <!-- Midtrans Direct Payment Trigger -->
                        <div class="pt-2">
                            <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-6 sm:p-8 text-center space-y-4">
                                <div class="max-w-md mx-auto space-y-3">
                                    <div class="w-12 h-12 bg-emerald-50 text-[#1B5E43] rounded-2xl flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Pembayaran Praktis via Midtrans</h3>
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        Pilih metode pembayaran (Transfer Virtual Account, QRIS Gopay / ShopeePay, atau Kartu Kredit) langsung dari jendela pop-up Midtrans yang muncul setelah menekan tombol di bawah.
                                    </p>
                                    
                                    <button id="pay-button" class="w-full bg-[#1B5E43] hover:bg-[#154b35] text-white font-bold py-4 px-6 rounded-xl shadow-md shadow-[#1B5E43]/20 transition duration-200 flex items-center justify-center gap-2 text-base cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Bayar Sekarang (Buka Midtrans)
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Check Status Button -->
                        <div class="pt-2">
                            <button onclick="window.location.reload();" class="w-full py-3 px-4 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2 transition">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Cek status pembayaran
                            </button>
                        </div>

                    </div>

                    <!-- Card Footer note -->
                    <div class="bg-gray-50/70 border-t border-gray-100 px-6 py-3.5 text-center text-[11px] text-gray-400 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Transaksi aman dan terverifikasi otomatis melalui integrasi resmi Midtrans Payment.
                    </div>
                </div>
            </div>

            <!-- Right Card: Detail Pesanan (4 cols) -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-5">
                
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h3 class="text-base font-bold text-gray-900">Detail pesanan</h3>
                        <span class="text-xs font-mono font-bold text-[#1B5E43] bg-emerald-50 px-2.5 py-1 rounded-md">
                            #CP-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <div class="space-y-3.5 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Pemesan</span>
                            <span class="font-bold text-gray-900 text-right">{{ $booking->user->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Meja</span>
                            <span class="font-bold text-gray-900 text-right">
                                {{ $booking->billiardTable->table_number }} · {{ $booking->billiardTable->type }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Tanggal</span>
                            <span class="font-bold text-gray-900 text-right">
                                {{ \Carbon\Carbon::parse($booking->start_time)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Waktu</span>
                            <span class="font-bold text-gray-900 text-right">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H.i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H.i') }} WIB
                            </span>
                        </div>
                        @php
                            $duration = \Carbon\Carbon::parse($booking->start_time)->diffInHours(\Carbon\Carbon::parse($booking->end_time));
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Durasi</span>
                            <span class="font-bold text-gray-900 text-right">{{ $duration }} jam</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3 text-sm">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Total booking</span>
                            <span class="font-medium text-gray-900">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-gray-900">
                            <span>{{ ($dpType ?? 'percentage') === 'fixed' ? 'DP (Nominal tetap)' : 'DP ' . ($dpPercentage ?? 30) . '%' }}</span>
                            <span class="text-[#1B5E43] text-base">Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600 text-xs pt-1 border-t border-dashed border-gray-200">
                            <span>Pelunasan di lokasi</span>
                            <span class="font-semibold text-gray-800">Rp{{ number_format($booking->total_price - $payment->gross_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Green Notification Box -->
                <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 flex gap-3 text-xs text-emerald-900 leading-relaxed">
                    <svg class="w-5 h-5 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-bold text-[#1B5E43] mb-0.5">Konfirmasi Otomatis</p>
                        Setelah DP terverifikasi, status pesanan berubah menjadi <strong>DP Lunas</strong>. Detail pesanan Anda dapat diakses di Riwayat Booking.
                    </div>
                </div>

                <!-- Action Button Back -->
                <a href="{{ route('booking.history') }}" class="block w-full py-3 px-4 bg-white border border-gray-200 rounded-xl text-center text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                    Kembali ke riwayat booking
                </a>

                <p class="text-center text-xs text-gray-400">
                    Kendala pembayaran? <a href="https://wa.me/6281288881908" target="_blank" class="text-gray-600 hover:underline">Hubungi WhatsApp</a>
                </p>

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

    <!-- Midtrans Snap Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        // Countdown Timer
        const bookingCreatedAt = new Date("{{ $booking->created_at->toIso8601String() }}").getTime();
        const TIMEOUT_MS = {{ $expiryMinutes }} * 60 * 1000;
        const expireAt = bookingCreatedAt + TIMEOUT_MS;
        const historyUrl = "{{ route('booking.history') }}";
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(function () {
            const now = new Date().getTime();
            const remaining = expireAt - now;

            if (remaining <= 0) {
                clearInterval(timer);
                countdownEl.textContent = '00:00';
                alert('Waktu pembayaran telah habis. Pesanan otomatis dibatalkan.');
                window.location.href = historyUrl;
                return;
            }

            const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
            countdownEl.textContent =
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            if (remaining < 3 * 60 * 1000) {
                countdownEl.style.color = '#dc2626';
            }
        }, 1000);

        // Midtrans Snap Modal Trigger
        document.getElementById('pay-button').onclick = function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    clearInterval(timer);
                    window.location.href = "{{ route('booking.history') }}";
                },
                onPending: function (result) {
                    alert("Menunggu penyelesaian pembayaran Anda.");
                    window.location.href = "{{ route('booking.history') }}";
                },
                onError: function (result) {
                    alert("Pembayaran gagal! Silakan coba lagi.");
                },
                onClose: function () {
                    // Modal ditutup
                }
            });
        };
    </script>
</body>
</html>