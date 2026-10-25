<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking {{ $billiardTable->table_number }} — Rackly Billiard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
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
                <div class="relative" x-data="{ userMenu: false }">
                    <button @click="userMenu = !userMenu" class="flex items-center gap-2 p-1 pl-2 pr-3 rounded-full hover:bg-gray-100 border border-transparent hover:border-gray-200 transition">
                        <div class="w-8 h-8 rounded-full bg-[#E8F3EE] text-[#1B5E43] font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="text-sm font-semibold text-gray-800 hidden sm:inline">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="userMenu" @click.outside="userMenu = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50">
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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-1"
        x-data="bookingManager({{ $billiardTable->price_per_hour }}, '{{ $dpType ?? 'percentage' }}', {{ $dpPercentage ?? 30 }}, {{ $dpNominal ?? 50000 }}, {{ json_encode($existingBookings ?? []) }})">

        <!-- Breadcrumb & Header with Step Indicator (Sesuai Referensi Figma) -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
            <div>
                <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">BERANDA / BOOKING MEJA</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Rencanakan permainanmu.</h1>
                <p class="text-gray-500 mt-2 text-sm sm:text-base">Pilih jadwal yang pas. Kami siapkan mejanya.</p>
            </div>

            <!-- Step Indicator -->
            <div class="hidden sm:flex items-center gap-3 text-xs sm:text-sm text-gray-500 shrink-0">
                <div class="flex items-center gap-2 font-bold text-gray-900">
                    <div class="w-7 h-7 rounded-full bg-[#1B5E43] text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        1
                    </div>
                    <span>Pilih jadwal</span>
                </div>
                <div class="w-8 sm:w-14 h-px bg-gray-200"></div>
                <div class="flex items-center gap-2 text-gray-400 font-medium">
                    <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs">
                        2
                    </div>
                    <span>Pembayaran DP</span>
                </div>
                <div class="w-8 sm:w-14 h-px bg-gray-200"></div>
                <div class="flex items-center gap-2 text-gray-400 font-medium">
                    <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs">
                        3
                    </div>
                    <span>Siap bermain</span>
                </div>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left Column: Selection Cards (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Card 1: Meja Pilihanmu -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7">
                    <h2 class="text-base font-bold text-gray-900 mb-4">Meja pilihanmu</h2>

                    <div class="flex flex-col sm:flex-row gap-5 items-start sm:items-center">
                        <div class="w-full sm:w-44 h-28 rounded-xl overflow-hidden bg-gray-900 shrink-0 shadow-sm relative">
                            <img src="/images/Table photograph.png" alt="{{ $billiardTable->table_number }}" class="w-full h-full object-cover">
                        </div>

                        <div class="flex-1 space-y-1">
                            <div class="flex items-center gap-2.5">
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $billiardTable->table_number }} · {{ $billiardTable->type }}
                                </h3>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Tersedia
                                </span>
                            </div>

                            <p class="text-xs text-gray-500">Area utama · 9 kaki · 2–4 pemain</p>

                            <div class="pt-1">
                                <span class="text-2xl font-extrabold text-[#1B5E43] tracking-tight">
                                    Rp{{ number_format($billiardTable->price_per_hour, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-gray-400 font-normal"> / jam</span>
                            </div>

                            <div class="pt-1">
                                <a href="{{ url('/#tables') }}" class="text-xs font-bold text-[#1B5E43] hover:underline inline-flex items-center gap-1">
                                    Ganti meja
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tanggal & Waktu Bermain -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7 space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Tanggal & waktu bermain</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Semua jadwal menggunakan Waktu Indonesia Barat (WIB).</p>
                    </div>

                    <form action="{{ route('bookings.store') }}" method="POST" id="booking-form">
                        @csrf
                        <input type="hidden" name="table_id" value="{{ $billiardTable->id }}">
                        <input type="hidden" name="booking_date" x-model="selectedDate">
                        <input type="hidden" name="start_time" x-model="startTime">
                        <input type="hidden" name="end_time" x-model="endTime">

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

                            <!-- Left: Interactive Calendar (5 cols) -->
                            <div class="md:col-span-5 bg-gray-50/70 border border-gray-200/70 rounded-2xl p-4 sm:p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold text-gray-900 capitalize" x-text="calendarMonthName"></span>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="prevMonth()" class="p-1 rounded-lg hover:bg-gray-200 text-gray-500 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="nextMonth()" class="p-1 rounded-lg hover:bg-gray-200 text-gray-500 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Days of Week Header -->
                                <div class="grid grid-cols-7 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                    <span>Sen</span>
                                    <span>Sel</span>
                                    <span>Rab</span>
                                    <span>Kam</span>
                                    <span>Jum</span>
                                    <span>Sab</span>
                                    <span>Min</span>
                                </div>

                                <!-- Calendar Day Matrix -->
                                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                    <template x-for="(day, idx) in calendarDays" :key="idx">
                                        <div class="aspect-square flex items-center justify-center">
                                            <button type="button"
                                                x-show="day.dayNumber"
                                                @click="if(!day.disabled) selectDate(day.dateString)"
                                                :disabled="day.disabled"
                                                :class="{
                                                        'bg-[#1B5E43] text-white font-extrabold shadow-sm': selectedDate === day.dateString,
                                                        'text-gray-900 hover:bg-emerald-100 font-semibold': !day.disabled && selectedDate !== day.dateString,
                                                        'text-gray-300 cursor-not-allowed': day.disabled
                                                    }"
                                                class="w-8 h-8 rounded-full flex items-center justify-center transition"
                                                x-text="day.dayNumber">
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Right: Time Selection & Slot Grid (7 cols) -->
                            <div class="md:col-span-7 space-y-5">

                                <div class="border-b border-gray-100 pb-3">
                                    <span class="text-xs font-bold text-gray-900 block" x-text="formatFullDate(selectedDate)"></span>
                                </div>

                                <!-- 2 Dropdowns: Jam Mulai & Jam Selesai -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jam Mulai</label>
                                        <select x-model="startTime" @change="onStartTimeChange()"
                                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                            <option value="">-- Jam Mulai --</option>
                                            <template x-for="slot in availableStartSlots" :key="slot">
                                                <option :value="slot" x-text="slot + ' WIB'"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jam Selesai</label>
                                        <select x-model="endTime" :disabled="!startTime"
                                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">-- Jam Selesai --</option>
                                            <template x-for="slot in availableEndSlots" :key="slot">
                                                <option :value="slot" x-text="slot + ' WIB'"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-400">
                                    Ketersediaan {{ $billiardTable->table_number }} pada tanggal ini:
                                </p>

                                <!-- Time Slots Grid (Display Only - Tidak Bisa Dipencet) -->
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="slot in allDisplaySlots" :key="slot.time">
                                        <div :class="{
                                                'bg-[#1B5E43] border-[#1B5E43] text-white font-extrabold shadow-sm': isSlotSelected(slot.time),
                                                'bg-red-50 border-red-200 text-red-500 font-semibold': slot.isBooked,
                                                'bg-gray-100 border-gray-200 text-gray-400 font-medium': slot.isPast && !slot.isBooked,
                                                'bg-white border-gray-200 text-gray-700 font-medium': !slot.isBooked && !slot.isPast && !isSlotSelected(slot.time)
                                            }"
                                            class="border rounded-xl py-2.5 text-xs text-center transition cursor-default select-none pointer-events-none">
                                            <span x-text="slot.time"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Legend -->
                                <div class="flex items-center gap-4 text-[11px] text-gray-500 pt-1">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded bg-white border border-gray-300 inline-block"></span>
                                        Tersedia
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded bg-[#1B5E43] inline-block"></span>
                                        Pilihanmu
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded bg-red-100 border border-red-300 inline-block"></span>
                                        Terisi
                                    </span>
                                </div>

                                <!-- Availability Notice -->
                                <template x-if="isValidTime">
                                    <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-xl p-3 text-xs text-emerald-900 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#1B5E43] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>
                                            Slot <strong x-text="startTime + ' – ' + endTime + ' WIB'"></strong> tersedia. Durasi minimum 1 jam, kelipatan 1 jam.
                                        </span>
                                    </div>
                                </template>

                            </div>

                        </div>

                        <!-- Card 3: Info Pemesan -->
                        <div class="border-t border-gray-100 pt-6 mt-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Informasi pemesan</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                                    <input type="text" value="{{ Auth::user()->name }}" disabled
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                                    <input type="email" value="{{ Auth::user()->email }}" disabled
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 cursor-not-allowed">
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2">Data diambil dari profil akunmu. Ubah melalui halaman Profil Saya.</p>
                        </div>

                    </form>
                </div>

            </div>

            <!-- Right Column: Ringkasan Booking (4 cols) -->
            <div class="lg:col-span-4 space-y-5">

                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-5 sticky top-24">

                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-900">Ringkasan booking</h3>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">{{ $billiardTable->table_number }} · {{ $billiardTable->type }}</span>
                            <span class="font-bold text-gray-900">Rp{{ number_format($billiardTable->price_per_hour, 0, ',', '.') }} / jam</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Tanggal</span>
                            <span class="font-bold text-gray-900" x-text="formatFullDate(selectedDate) || '—'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Waktu</span>
                            <span class="font-bold text-gray-900" x-text="startTime && endTime ? startTime + ' – ' + endTime : '—'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Durasi bermain</span>
                            <span class="font-bold text-gray-900" x-text="durationHours > 0 ? durationHours + ' jam' : '—'"></span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-3 space-y-2 text-xs sm:text-sm">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-900" x-text="formatRupiah(totalPrice)"></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Biaya layanan</span>
                            <span class="font-semibold text-gray-900">Rp0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-extrabold text-gray-900 pt-1">
                            <span>Total booking</span>
                            <span class="text-base text-gray-900" x-text="formatRupiah(totalPrice)"></span>
                        </div>
                    </div>

                    <!-- Highlight DP Box -->
                    <template x-if="isValidTime">
                        <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block"
                                          x-text="dpType === 'fixed' ? 'DP dibayar sekarang · Nominal tetap' : ('DP dibayar sekarang · ' + dpPercentage + '%')">
                                    </span>
                                    <span class="text-2xl font-black text-[#1B5E43] tracking-tight block mt-0.5" x-text="formatRupiah(dpAmount)"></span>
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-500 leading-tight">
                                Sisa <strong class="text-gray-800" x-text="formatRupiah(totalPrice - dpAmount)"></strong> dilunasi di kasir lokasi sebelum bermain.
                            </p>
                        </div>
                    </template>

                    <!-- Submit Button -->
                    <div class="space-y-2.5 pt-1">
                        <button type="submit" form="booking-form" :disabled="!isValidTime"
                            class="w-full bg-[#1B5E43] hover:bg-[#154b35] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3.5 px-6 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">
                            Lanjut ke pembayaran &rarr;
                        </button>

                        <p class="text-[11px] text-gray-400 text-center leading-tight">
                            Jadwal dikonfirmasi setelah pembayaran DP berhasil.
                        </p>
                    </div>

                    <!-- Note Datang 10 Menit (Tanpa Note H-3 sesuai aturan) -->
                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3.5 text-xs text-amber-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Datang 10 menit sebelum jadwal.</span>
                    </div>

                    <p class="text-[10px] text-gray-400 text-center leading-normal">
                        Dengan melanjutkan, kamu menyetujui syarat booking dan kebijakan klub.
                    </p>

                </div>

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

    <script>
        function bookingManager(pricePerHour, dpType, dpPct, dpNominal, existingBookings) {
            return {
                pricePerHour,
                dpType: dpType || 'percentage',
                dpPercentage: dpPct || 30,
                dpNominal: dpNominal || 50000,
                existingBookings: existingBookings || [],
                selectedDate: '{{ date('Y-m-d') }}',
                calYear: new Date().getFullYear(),
                calMonth: new Date().getMonth(), // 0-11
                startTime: '',
                endTime: '',

                init() {
                    const todayDate = new Date();
                    this.calYear = todayDate.getFullYear();
                    this.calMonth = todayDate.getMonth();
                    this.autoSelectAvailableSlot();
                },

                // Calendar Navigation
                prevMonth() {
                    if (this.calMonth === 0) {
                        this.calMonth = 11;
                        this.calYear--;
                    } else {
                        this.calMonth--;
                    }
                },

                nextMonth() {
                    if (this.calMonth === 11) {
                        this.calMonth = 0;
                        this.calYear++;
                    } else {
                        this.calMonth++;
                    }
                },

                get calendarMonthName() {
                    const d = new Date(this.calYear, this.calMonth, 1);
                    return d.toLocaleDateString('id-ID', {
                        month: 'long',
                        year: 'numeric'
                    });
                },

                get calendarDays() {
                    const firstDayIndex = new Date(this.calYear, this.calMonth, 1).getDay(); // 0 is Sunday
                    // Convert Sunday (0) to 6, Monday (1) to 0
                    const startDayOffset = (firstDayIndex === 0 ? 6 : firstDayIndex - 1);
                    const daysInMonth = new Date(this.calYear, this.calMonth + 1, 0).getDate();

                    const todayStr = '{{ date('Y-m-d') }}';
                    const days = [];

                    // Leading empty days
                    for (let i = 0; i < startDayOffset; i++) {
                        days.push({
                            dayNumber: null,
                            disabled: true
                        });
                    }

                    // Days in current month
                    for (let d = 1; d <= daysInMonth; d++) {
                        const mStr = String(this.calMonth + 1).padStart(2, '0');
                        const dStr = String(d).padStart(2, '0');
                        const dateString = `${this.calYear}-${mStr}-${dStr}`;
                        const isPast = dateString < todayStr;
                        days.push({
                            dayNumber: d,
                            dateString,
                            disabled: isPast
                        });
                    }

                    return days;
                },

                selectDate(dateStr) {
                    this.selectedDate = dateStr;
                    this.startTime = '';
                    this.endTime = '';
                    this.autoSelectAvailableSlot();
                },

                // Operating Hours: 10:00 to 23:00
                get allHourList() {
                    return [
                        '10:00', '11:00', '12:00', '13:00', '14:00', '15:00',
                        '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'
                    ];
                },

                get allDisplaySlots() {
                    const todayStr = '{{ date('Y-m-d') }}';
                    const isToday = this.selectedDate === todayStr;
                    const nowH = new Date().getHours();

                    return this.allHourList.map(timeStr => {
                        const h = parseInt(timeStr.split(':')[0]);
                        const isPast = isToday && h <= nowH;
                        const isBooked = this.checkIfSlotBooked(timeStr);
                        return {
                            time: timeStr,
                            isPast,
                            isBooked
                        };
                    });
                },

                checkIfSlotBooked(timeStr) {
                    const targetHour = parseInt(timeStr.split(':')[0]);
                    return this.existingBookings.some(b => {
                        if (b.date !== this.selectedDate) return false;
                        const bStart = parseInt(b.start.split(':')[0]);
                        const bEnd = parseInt(b.end.split(':')[0]);
                        return targetHour >= bStart && targetHour < bEnd;
                    });
                },

                get availableStartSlots() {
                    return this.allDisplaySlots
                        .filter(s => !s.isPast && !s.isBooked)
                        .map(s => s.time);
                },

                get availableEndSlots() {
                    if (!this.startTime) return [];
                    const startH = parseInt(this.startTime.split(':')[0]);
                    const endSlots = [];

                    for (let h = startH + 1; h <= 23; h++) {
                        const timeStr = String(h).padStart(2, '0') + ':00';
                        // Check if block between startH and h is booked
                        let hasCollision = false;
                        for (let checkH = startH; checkH < h; checkH++) {
                            if (this.checkIfSlotBooked(String(checkH).padStart(2, '0') + ':00')) {
                                hasCollision = true;
                                break;
                            }
                        }
                        if (hasCollision) break;
                        endSlots.push(timeStr);
                    }
                    return endSlots;
                },

                onStartTimeChange() {
                    if (!this.startTime) {
                        this.endTime = '';
                        return;
                    }
                    const availableEnds = this.availableEndSlots;
                    if (availableEnds.length > 0) {
                        if (this.endTime && availableEnds.includes(this.endTime)) {
                            // Pertahankan jam selesai jika masih dalam pilihan valid
                        } else if (availableEnds.length >= 2) {
                            this.endTime = availableEnds[1]; // default 2 jam
                        } else {
                            this.endTime = availableEnds[0]; // default 1 jam
                        }
                    } else {
                        this.endTime = '';
                    }
                },

                isSlotSelected(timeStr) {
                    if (!this.startTime) return false;
                    const h = parseInt(timeStr.split(':')[0]);
                    const sH = parseInt(this.startTime.split(':')[0]);
                    if (!this.endTime) return h === sH;
                    const eH = parseInt(this.endTime.split(':')[0]);
                    return h >= sH && h < eH;
                },

                autoSelectAvailableSlot() {
                    const available = this.availableStartSlots;
                    if (available.length > 0) {
                        this.startTime = available[0];
                        this.onStartTimeChange();
                    } else {
                        this.startTime = '';
                        this.endTime = '';
                    }
                },

                get durationHours() {
                    if (!this.startTime || !this.endTime) return 0;
                    const s = parseInt(this.startTime.split(':')[0]);
                    const e = parseInt(this.endTime.split(':')[0]);
                    return e > s ? e - s : 0;
                },

                get isValidTime() {
                    return this.durationHours > 0;
                },

                get totalPrice() {
                    return this.durationHours * this.pricePerHour;
                },

                get dpAmount() {
                    if (this.dpType === 'fixed') {
                        return Math.min(this.dpNominal, this.totalPrice);
                    }
                    return this.totalPrice * (this.dpPercentage / 100);
                },

                formatFullDate(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr + 'T00:00:00');
                    return d.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                },

                formatRupiah(num) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(num || 0);
                }
            };
        }
    </script>
</body>

</html>