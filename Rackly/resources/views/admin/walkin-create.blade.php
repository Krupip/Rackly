@extends('layouts.admin')

@section('title', 'Tambah Pesanan Walk-in — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Tambah pesanan walk-in')

@section('content')
<div x-data="walkInManager('{{ $openHour ?? '10:00' }}', '{{ $closeHour ?? '23:00' }}')" x-init="init()" class="space-y-8">
    
    <!-- Header -->
    <div>
        <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">OPERASIONAL / PESANAN BARU</span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Tambah pesanan walk-in</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Catat pelanggan yang datang langsung. Jadwal dan pembayaran dalam satu tempat.</p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.walkin.store') }}" method="POST" id="walkin-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Forms (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Card 1: Informasi Pelanggan -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Informasi pelanggan</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Pelanggan walk-in tidak harus memiliki akun website.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="guest_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Nama Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="guest_name" name="guest_name" x-model="customerName" required
                                   value="{{ old('guest_name') }}" placeholder="Contoh: Rizky Maulana"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                            @error('guest_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Nomor WhatsApp
                            </label>
                            <input type="text" id="phone" name="phone" placeholder="0812 3456 7890"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Email (opsional)
                        </label>
                        <input type="email" id="email" name="email" placeholder="Tambahkan alamat email pelanggan jika ingin mengirim nota"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                    </div>
                </div>                <!-- Card 2: Jadwal & Meja -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7 space-y-6">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Jadwal & meja</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Tentukan waktu bermain untuk mengecek ketersediaan meja secara otomatis.</p>
                    </div>

                    <!-- Jadwal Bermain (Tanggal, Jam Mulai, Jam Selesai) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Tanggal Bermain -->
                        <div>
                            <label for="booking_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Tanggal Bermain <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="booking_date" name="booking_date" x-model="selectedDate"
                                   min="{{ date('Y-m-d') }}" @change="onDateChange()" required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                            @error('booking_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jam Mulai -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <button type="button" @click="openStart = !openStart; openEnd = false"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 flex justify-between items-center text-left focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                    <span x-text="startTime ? startTime + ' WIB' : '-- Jam Mulai --'"></span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openStart" @click.outside="openStart = false" class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-56 overflow-y-auto py-1 text-xs">
                                    <template x-for="item in availableStartHours" :key="item">
                                        <button type="button"
                                                @click="if(!isPastHour(item)) { selectStartTime(item); }"
                                                :disabled="isPastHour(item)"
                                                class="w-full px-4 py-2.5 text-left transition flex items-center justify-between font-semibold"
                                                :class="{
                                                    'bg-emerald-50 text-[#1B5E43] font-bold': startTime === item,
                                                    'opacity-40 bg-gray-50 text-gray-400 cursor-not-allowed': isPastHour(item),
                                                    'text-gray-700 hover:bg-[#1B5E43] hover:text-white': !isPastHour(item) && startTime !== item
                                                }">
                                            <span x-text="item + ' WIB'"></span>
                                            <span x-show="isPastHour(item)" class="text-[10px] text-gray-400 font-normal italic">(Sudah lewat)</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="start_time" x-model="startTime" required>
                            @error('start_time')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jam Selesai -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <button type="button" @click="if(startTime){ openEnd = !openEnd; openStart = false; }"
                                        :disabled="!startTime"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 flex justify-between items-center text-left disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                    <span x-text="endTime ? endTime + ' WIB' : (startTime ? '-- Jam Selesai --' : 'Pilih Jam Mulai Dulu')"></span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openEnd && startTime" @click.outside="openEnd = false" class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-56 overflow-y-auto py-1 text-xs">
                                    <template x-for="item in availableEndHours" :key="item">
                                        <button type="button"
                                                @click="if(!isPastHour(item, true)) { endTime = item; openEnd = false; onEndTimeChange(); }"
                                                :disabled="isPastHour(item, true)"
                                                class="w-full px-4 py-2.5 text-left transition flex items-center justify-between font-semibold"
                                                :class="{
                                                    'bg-emerald-50 text-[#1B5E43] font-bold': endTime === item,
                                                    'opacity-40 bg-gray-50 text-gray-400 cursor-not-allowed': isPastHour(item, true),
                                                    'text-gray-700 hover:bg-[#1B5E43] hover:text-white': !isPastHour(item, true) && endTime !== item
                                                }">
                                            <span x-text="item + ' WIB'"></span>
                                            <span x-show="isPastHour(item, true)" class="text-[10px] text-gray-400 font-normal italic">(Sudah lewat)</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="end_time" x-model="endTime" required>
                            @error('end_time')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Warning Alert jika Meja Terpilih Bentrok -->
                    <template x-if="tableAlert">
                        <div class="p-3.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="tableAlert"></span>
                        </div>
                    </template>

                    <!-- Pemilihan Meja (Hanya Menampilkan Meja Tersedia) -->
                    <div class="space-y-3 pt-2 border-t border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <label for="billiard_table_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Pilih Meja Biliar <span class="text-red-500">*</span>
                                </label>
                                <template x-if="startTime && endTime">
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Menampilkan meja yang tersedia untuk jam <strong x-text="startTime + ' – ' + endTime + ' WIB'"></strong>
                                    </p>
                                </template>
                                <template x-if="!startTime || !endTime">
                                    <p class="text-xs text-gray-400 mt-0.5">Pilih jam mulai dan selesai untuk memfilter meja yang tersedia.</p>
                                </template>
                            </div>

                            <template x-if="startTime && endTime">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#1B5E43] border border-emerald-200/80 self-start sm:self-auto">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span x-text="displayedTables.length + ' Meja Tersedia'"></span>
                                </span>
                            </template>
                        </div>

                        <!-- Dropdown Select (Synced dengan Grid, Hanya Meja Tersedia) -->
                        <div>
                            <select id="billiard_table_id" name="billiard_table_id" x-model="selectedTableId" @change="updateTableSelection($event.target)" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <option value="">-- Pilih Meja Biliar (Hanya Meja Tersedia) --</option>
                                <template x-for="table in displayedTables" :key="table.id">
                                    <option :value="table.id"
                                            x-text="formatTableName(table.table_number) + ' · ' + table.type + ' (' + formatRupiah(table.price_per_hour) + '/jam)'">
                                    </option>
                                </template>
                            </select>
                            @error('billiard_table_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Scrollable Container untuk Kartu Meja -->
                        <div class="max-h-72 sm:max-h-80 overflow-y-auto pr-1.5 -mr-1.5 pt-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 p-0.5">
                                <template x-for="table in displayedTables" :key="table.id">
                                    <div @click="selectTable(table)"
                                         class="border-2 rounded-xl p-4 transition relative flex flex-col justify-between cursor-pointer"
                                         :class="{
                                             'border-[#1B5E43] bg-emerald-50/60 shadow-sm ring-2 ring-[#1B5E43]/20': selectedTableId == table.id,
                                             'border-gray-200 bg-white hover:border-[#1B5E43]/60 hover:shadow-xs': selectedTableId != table.id
                                         }">
                                        
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div>
                                                <div class="text-sm font-extrabold text-gray-900" x-text="formatTableName(table.table_number)"></div>
                                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md mt-1 inline-block"
                                                      :class="table.type === 'VIP' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600'"
                                                      x-text="table.type"></span>
                                            </div>
                                            
                                            <!-- Status Badge -->
                                            <div>
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    Tersedia
                                                </span>
                                            </div>
                                        </div>

                                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-xs mt-auto">
                                            <span class="font-extrabold text-gray-900" x-text="formatRupiah(table.price_per_hour) + '/jam'"></span>
                                            <span class="text-[11px] text-[#1B5E43] font-semibold flex items-center gap-1">
                                                <span x-show="selectedTableId == table.id">Terpilih ✓</span>
                                                <span x-show="selectedTableId != table.id" class="text-gray-400">Pilih meja →</span>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Pesan jika tidak ada meja yang tersedia -->
                        <template x-if="displayedTables.length === 0">
                            <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/60">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-bold text-gray-700">Tidak ada meja yang tersedia pada jam ini</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">Semua meja telah terisi pada rentang waktu yang dipilih. Silakan pilih jam lain.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Dynamic Availability Notice -->
                    <template x-if="selectedTableName && startTime && endTime && isTableAvailable(selectedTableId)">
                        <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-xl text-xs text-emerald-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#1B5E43] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>
                                <strong x-text="formatTableName(selectedTableName)"></strong> tersedia pada <strong x-text="startTime + ' – ' + endTime + ' WIB'"></strong>. Durasi <strong x-text="durationHours + ' jam'"></strong>, tarif <span x-text="formatRupiah(pricePerHour) + '/jam'"></span>.
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Card 3: Pembayaran di Kasir -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Pembayaran di kasir</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Walk-in menggunakan pembayaran penuh di lokasi, tanpa melalui DP online.</p>
                    </div>

                    <!-- Metode Pembayaran Tabs -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" @click="paymentMethod = 'Tunai'"
                                    :class="paymentMethod === 'Tunai' ? 'border-[#1B5E43] bg-emerald-50/50 text-[#1B5E43] font-bold' : 'border-gray-200 bg-gray-50 text-gray-700'"
                                    class="border-2 rounded-xl py-3 px-4 text-xs flex items-center justify-center gap-2 transition">
                                <span class="w-2.5 h-2.5 rounded-full" :class="paymentMethod === 'Tunai' ? 'bg-[#1B5E43]' : 'bg-gray-300'"></span>
                                Tunai
                            </button>
                            <button type="button" @click="paymentMethod = 'QRIS'"
                                    :class="paymentMethod === 'QRIS' ? 'border-[#1B5E43] bg-emerald-50/50 text-[#1B5E43] font-bold' : 'border-gray-200 bg-gray-50 text-gray-700'"
                                    class="border-2 rounded-xl py-3 px-4 text-xs flex items-center justify-center gap-2 transition">
                                <span class="w-2.5 h-2.5 rounded-full" :class="paymentMethod === 'QRIS' ? 'bg-[#1B5E43]' : 'bg-gray-300'"></span>
                                QRIS
                            </button>
                            <button type="button" @click="paymentMethod = 'Transfer bank'"
                                    :class="paymentMethod === 'Transfer bank' ? 'border-[#1B5E43] bg-emerald-50/50 text-[#1B5E43] font-bold' : 'border-gray-200 bg-gray-50 text-gray-700'"
                                    class="border-2 rounded-xl py-3 px-4 text-xs flex items-center justify-center gap-2 transition">
                                <span class="w-2.5 h-2.5 rounded-full" :class="paymentMethod === 'Transfer bank' ? 'bg-[#1B5E43]' : 'bg-gray-300'"></span>
                                Transfer bank
                            </button>
                        </div>
                    </div>

                    <!-- Uang Diterima & Kembalian (jika tunai) -->
                    <template x-if="paymentMethod === 'Tunai'">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="cash_received" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Uang Diterima</label>
                                <input type="number" id="cash_received" x-model="cashReceived" placeholder="Contoh: 100000"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kembalian</label>
                                <div class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm font-extrabold text-gray-800"
                                     x-text="changeAmount >= 0 ? formatRupiah(changeAmount) : 'Uang Kurang'">
                                </div>
                            </div>
                        </div>
                    </template>

                    <div>
                        <label for="notes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan (opsional)</label>
                        <input type="text" id="notes" name="notes" placeholder="Contoh: Datang rombongan, stik tambahan"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                    </div>
                </div>

            </div>

            <!-- Right Column: Ringkasan Pesanan (4 cols) -->
            <div class="lg:col-span-4 space-y-5">
                
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-5 sticky top-24">
                    
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-900">Ringkasan pesanan</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Pesanan walk-in baru
                        </span>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pelanggan</span>
                            <span class="font-bold text-gray-900" x-text="customerName || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Meja</span>
                            <span class="font-bold text-gray-900" x-text="selectedTableName ? formatTableName(selectedTableName) : '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tanggal</span>
                            <span class="font-bold text-gray-900" x-text="selectedDate || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jam</span>
                            <span class="font-bold text-gray-900" x-text="startTime && endTime ? startTime + ' – ' + endTime : '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tarif × durasi</span>
                            <span class="font-medium text-gray-700" x-text="durationHours > 0 && pricePerHour > 0 ? formatRupiah(pricePerHour) + ' × ' + durationHours + ' jam' : '-'"></span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-900">Total booking</span>
                        <span class="text-xl font-black text-gray-900" x-text="formatRupiah(totalPrice)">Rp0</span>
                    </div>

                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>Metode pembayaran</span>
                        <span class="font-bold text-gray-800" x-text="paymentMethod"></span>
                    </div>

                    <!-- Green Notice Box -->
                    <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-xl p-3.5 text-xs text-emerald-900 leading-relaxed">
                        <p class="font-bold text-[#1B5E43] mb-0.5">Lunas saat disimpan</p>
                        <span x-text="formatRupiah(totalPrice)"></span> langsung tercatat di pendapatan kasir hari ini. Jadwal meja dikonfirmasi otomatis.
                    </div>

                    <!-- Submit & Cancel Buttons -->
                    <div class="space-y-2 pt-2">
                        <button type="submit" :disabled="!isFormValid"
                                class="w-full bg-[#1B5E43] hover:bg-[#154b35] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3.5 px-6 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan pesanan walk-in
                        </button>

                        <a href="{{ route('admin.bookings') }}" class="block w-full py-2.5 text-center text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition">
                            Batal
                        </a>
                    </div>

                </div>

                <!-- Bottom Notice -->
                <div class="bg-gray-50 border border-gray-200/60 rounded-xl p-4 text-xs text-gray-500 flex gap-2">
                    <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pastikan uang kasir/bukti transfer telah diterima. Pesanan ini langsung berstatus <strong>Lunas</strong>.</span>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
    function walkInManager(openHour, closeHour) {
        return {
            openStart: false,
            openEnd: false,
            openHour: openHour || '10:00',
            closeHour: closeHour || '23:00',
            customerName: '{{ old('guest_name') }}' || '',
            selectedTableId: '{{ old('billiard_table_id') }}' || '',
            selectedTableName: '',
            pricePerHour: 0,
            selectedDate: '{{ old('booking_date', date('Y-m-d')) }}',
            startTime: '{{ old('start_time') }}' || '',
            endTime: '{{ old('end_time') }}' || '',
            paymentMethod: 'Tunai',
            cashReceived: '',
            tableAlert: '',
            existingBookings: @json($existingBookings ?? []),
            tablesData: @json($tables ?? []),

            init() {
                this.updateTableSelection();
                if (this.startTime && this.isPastHour(this.startTime)) {
                    this.startTime = '';
                    this.endTime = '';
                }
                this.checkSelectedTableAvailability();
            },

            parseHour(timeStr, isEnd = false) {
                if (!timeStr) return 0;
                let h = parseInt(timeStr.split(':')[0], 10);
                if (isEnd && h === 0) h = 24;
                return h;
            },

            isPastHour(timeStr, isEnd = false) {
                if (!timeStr) return false;
                const todayStr = '{{ date('Y-m-d') }}';
                if (this.selectedDate < todayStr) return true;
                if (this.selectedDate > todayStr) return false;

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
                if (!this.startTime) return [];
                let start = parseInt(this.startTime.split(':')[0], 10);
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
                this.startTime = item;
                this.openStart = false;
                let startH = parseInt(item.split(':')[0], 10);
                let nextH = startH + 1;
                let nextTimeStr = nextH === 24 ? '00:00' : (nextH < 10 ? '0' : '') + nextH + ':00';
                if (!this.endTime || this.parseHour(this.endTime, true) <= startH) {
                    this.endTime = nextTimeStr;
                }
                this.checkSelectedTableAvailability();
            },

            onEndTimeChange() {
                this.checkSelectedTableAvailability();
            },

            onDateChange() {
                if (this.startTime && this.isPastHour(this.startTime)) {
                    this.startTime = '';
                    this.endTime = '';
                }
                this.checkSelectedTableAvailability();
            },

            isTableConflict(tableId) {
                if (!this.selectedDate || !this.startTime || !this.endTime) {
                    return false;
                }
                const sH = this.parseHour(this.startTime, false);
                const eH = this.parseHour(this.endTime, true);
                if (eH <= sH) return false;

                return this.existingBookings.some(b => {
                    if (parseInt(b.table_id) !== parseInt(tableId)) return false;
                    if (b.date !== this.selectedDate) return false;

                    const bStart = this.parseHour(b.start, false);
                    const bEnd   = this.parseHour(b.end, true);

                    return sH < bEnd && eH > bStart;
                });
            },

            isTableAvailable(tableId) {
                return !this.isTableConflict(tableId);
            },

            isTableDisabled(tableId) {
                if (!this.startTime || !this.endTime) return false;
                return this.isTableConflict(tableId);
            },

            getTableConflictInfo(tableId) {
                if (!this.selectedDate || !this.startTime || !this.endTime) return null;
                const sH = this.parseHour(this.startTime, false);
                const eH = this.parseHour(this.endTime, true);

                const conflicts = this.existingBookings.filter(b => {
                    if (parseInt(b.table_id) !== parseInt(tableId)) return false;
                    if (b.date !== this.selectedDate) return false;
                    const bStart = this.parseHour(b.start, false);
                    const bEnd   = this.parseHour(b.end, true);
                    return sH < bEnd && eH > bStart;
                });

                if (conflicts.length === 0) return null;
                return conflicts.map(c => `${c.start}–${c.end}`).join(', ');
            },

            selectTable(table) {
                if (this.isTableDisabled(table.id)) return;
                this.selectedTableId = table.id;
                this.pricePerHour = parseInt(table.price_per_hour);
                this.selectedTableName = table.table_number + ' · ' + table.type;
                this.tableAlert = '';
            },

            updateTableSelection(selectEl) {
                if (!this.selectedTableId) {
                    this.pricePerHour = 0;
                    this.selectedTableName = '';
                    return;
                }
                const table = this.tablesData.find(t => t.id == this.selectedTableId);
                if (table) {
                    this.pricePerHour = parseInt(table.price_per_hour);
                    this.selectedTableName = table.table_number + ' · ' + table.type;
                    if (this.isTableDisabled(table.id)) {
                        this.checkSelectedTableAvailability();
                    }
                }
            },

            checkSelectedTableAvailability() {
                if (this.selectedTableId && this.startTime && this.endTime) {
                    if (!this.isTableAvailable(this.selectedTableId)) {
                        const prevName = this.selectedTableName || 'yang dipilih';
                        this.selectedTableId = '';
                        this.selectedTableName = '';
                        this.pricePerHour = 0;
                        this.tableAlert = `${this.formatTableName(prevName)} tidak tersedia pada jam ${this.startTime} – ${this.endTime} WIB. Silakan pilih meja lain.`;
                    } else {
                        this.tableAlert = '';
                    }
                }
            },

            get displayedTables() {
                if (this.startTime && this.endTime) {
                    return this.tablesData.filter(t => this.isTableAvailable(t.id));
                }
                return this.tablesData;
            },

            get durationHours() {
                if (!this.startTime || !this.endTime) return 0;
                let s = this.parseHour(this.startTime, false);
                let e = this.parseHour(this.endTime, true);
                return e > s ? e - s : 0;
            },

            get totalPrice() {
                return this.durationHours * this.pricePerHour;
            },

            get changeAmount() {
                if (!this.cashReceived) return 0;
                return parseInt(this.cashReceived) - this.totalPrice;
            },

            get isFormValid() {
                return this.customerName.trim() !== '' &&
                       this.selectedTableId !== '' &&
                       this.selectedDate !== '' &&
                       this.durationHours > 0 &&
                       this.isTableAvailable(this.selectedTableId);
            },

            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(num);
            },

            formatTableName(val) {
                if (!val) return '';
                return val.toLowerCase().startsWith('meja') ? val : 'Meja ' + val;
            }
        };
    }
</script>
@endsection
