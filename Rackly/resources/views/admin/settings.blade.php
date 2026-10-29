@extends('layouts.admin')

@section('title', 'Pengaturan Uang Muka — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Pengaturan')

@section('content')
@php
    $currentDpType = $settings->get('dp_type')?->value ?? 'percentage';
    $currentDp = (int) ($settings->get('dp_percentage')?->value ?? 30);
    $currentDpNominal = (int) ($settings->get('dp_nominal')?->value ?? 50000);
    $currentExpiry = (int) ($settings->get('dp_expiry_minutes')?->value ?? 15);
    $currentOpenHour = $settings->get('open_hour')?->value ?? '10:00';
    $currentCloseHour = $settings->get('close_hour')?->value ?? '23:00';
@endphp

<div x-data="settingsSimulator('{{ $currentDpType }}', {{ $currentDp }}, {{ $currentDpNominal }}, {{ $currentExpiry }}, '{{ $currentOpenHour }}', '{{ $currentCloseHour }}')" class="space-y-8">
    
    <!-- Notification Toast: Perubahan Dibatalkan -->
    <div x-show="showResetNotice"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-20 right-6 z-50 bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 border border-gray-700/60 text-xs font-semibold"
         style="display: none;">
        <div class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <div>
            <p class="font-bold text-white text-xs">Perubahan dibatalkan</p>
            <p class="text-[11px] text-gray-300 font-normal">Semua pengaturan (termasuk jam buka & jam tutup) telah dikembalikan ke nilai semula.</p>
        </div>
        <button type="button" @click="showResetNotice = false" class="ml-2 text-gray-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    
    <!-- Header -->
    <div>
        <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">KONFIGURASI</span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Bisnis</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Tentukan DP booking online. Transparan untuk pelanggan, terkontrol untuk klub.</p>
    </div>

    <!-- Green Info Banner -->
    <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 flex gap-3 text-xs text-emerald-900 leading-relaxed">
        <svg class="w-5 h-5 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <template x-if="dpType === 'percentage'">
                <span>Aturan aktif: <strong>DP <span x-text="dpPct"></span>% dari total booking</strong>. Berlaku untuk seluruh meja Reguler dan VIP. Pesanan walk-in dibayar penuh di kasir tanpa DP.</span>
            </template>
            <template x-if="dpType === 'fixed'">
                <span>Aturan aktif: <strong>DP <span x-text="formatRupiah(dpNominal)"></span> (Nominal tetap) per pesanan booking</strong>. Berlaku untuk seluruh meja Reguler dan VIP. Pesanan walk-in dibayar penuh di kasir tanpa DP.</span>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Forms (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card 1: Besaran DP -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7 space-y-6">
                
                <div class="border-b border-gray-100 pb-3">
                    <h2 class="text-base font-bold text-gray-900">Besaran DP</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Perubahan hanya berlaku untuk pesanan baru setelah disimpan.</p>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6" @submit="isSubmitting = true">
                    @csrf
                    @method('PATCH')

                    <!-- Hidden dp_type input bound to Alpine dpType -->
                    <input type="hidden" name="dp_type" :value="dpType">

                    <!-- Metode Perhitungan DP Selection -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Metode Perhitungan DP</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Card Persentase -->
                            <div @click="dpType = 'percentage'"
                                 class="border-2 rounded-xl p-4 cursor-pointer transition select-none flex flex-col justify-between"
                                 :class="dpType === 'percentage' ? 'border-[#1B5E43] bg-emerald-50/40 shadow-sm' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition"
                                          :class="dpType === 'percentage' ? 'border-[#1B5E43] bg-[#1B5E43]' : 'border-gray-300 bg-white'">
                                        <span x-show="dpType === 'percentage'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    </span>
                                    <span class="text-sm font-bold"
                                          :class="dpType === 'percentage' ? 'text-[#1B5E43]' : 'text-gray-900'">
                                        Persentase
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 pl-6">Dihitung otomatis dari durasi dan total harga booking</p>
                            </div>

                            <!-- Card Nominal Tetap -->
                            <div @click="dpType = 'fixed'"
                                 class="border-2 rounded-xl p-4 cursor-pointer transition select-none flex flex-col justify-between"
                                 :class="dpType === 'fixed' ? 'border-[#1B5E43] bg-emerald-50/40 shadow-sm' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition"
                                          :class="dpType === 'fixed' ? 'border-[#1B5E43] bg-[#1B5E43]' : 'border-gray-300 bg-white'">
                                        <span x-show="dpType === 'fixed'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    </span>
                                    <span class="text-sm font-bold"
                                          :class="dpType === 'fixed' ? 'text-[#1B5E43]' : 'text-gray-900'">
                                        Nominal Tetap
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 pl-6">Nilai rupiah tetap per pesanan booking</p>
                            </div>
                        </div>
                    </div>

                    <!-- Input Persentase DP & Nominal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Persentase DP Input -->
                        <div>
                            <label for="dp_percentage" class="block text-xs font-bold uppercase tracking-wider mb-2"
                                   :class="dpType === 'percentage' ? 'text-gray-700' : 'text-gray-400'">
                                <span x-text="dpType === 'percentage' ? 'Persentase DP Aktif' : 'Persentase DP (tidak aktif)'"></span>
                                <span x-show="dpType === 'percentage'" class="text-red-500">*</span>
                            </label>
                            <div class="relative" @click="if(dpType !== 'percentage') dpType = 'percentage'">
                                <input type="number" id="dp_percentage" name="dp_percentage"
                                       x-model.number="dpPct" min="1" max="100"
                                       :required="dpType === 'percentage'"
                                       :readonly="dpType !== 'percentage'"
                                       :class="dpType === 'percentage' ? 'bg-gray-50 border-gray-200 text-gray-900 focus:bg-white focus:border-[#1B5E43]' : 'bg-gray-100 border-gray-200 text-gray-400 cursor-pointer'"
                                       class="w-full px-4 py-3 border rounded-xl text-base font-extrabold focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 transition pr-10">
                                <span class="absolute right-4 top-3.5 text-sm font-bold"
                                      :class="dpType === 'percentage' ? 'text-gray-400' : 'text-gray-300'">%</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Rentang 1–100% dari total booking.</p>
                            @error('dp_percentage')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nominal DP Input -->
                        <div>
                            <label for="dp_nominal" class="block text-xs font-bold uppercase tracking-wider mb-2"
                                   :class="dpType === 'fixed' ? 'text-gray-700' : 'text-gray-400'">
                                <span x-text="dpType === 'fixed' ? 'Nominal DP Aktif' : 'Nominal DP (tidak aktif)'"></span>
                                <span x-show="dpType === 'fixed'" class="text-red-500">*</span>
                            </label>
                            <div class="relative" @click="if(dpType !== 'fixed') dpType = 'fixed'">
                                <span class="absolute left-4 top-3.5 text-sm font-bold"
                                      :class="dpType === 'fixed' ? 'text-gray-500' : 'text-gray-300'">Rp</span>
                                <input type="number" id="dp_nominal" name="dp_nominal"
                                       x-model.number="dpNominal" min="1000"
                                       :required="dpType === 'fixed'"
                                       :readonly="dpType !== 'fixed'"
                                       :class="dpType === 'fixed' ? 'bg-gray-50 border-gray-200 text-gray-900 focus:bg-white focus:border-[#1B5E43]' : 'bg-gray-100 border-gray-200 text-gray-400 cursor-pointer'"
                                       class="w-full pl-11 pr-4 py-3 border rounded-xl text-base font-extrabold focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 transition">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Nominal rupiah tetap per booking (minimal Rp1.000, contoh: 15000, 50000).</p>
                            @error('dp_nominal')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Batas Waktu Pembayaran DP -->
                    <div>
                        <label for="dp_expiry_minutes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Batas Waktu Pembayaran DP
                        </label>
                        <select id="dp_expiry_minutes" name="dp_expiry_minutes" x-model="dpExpiry"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                            <option value="15">15 menit setelah pesanan dibuat (Standar)</option>
                            <option value="30">30 menit setelah pesanan dibuat</option>
                            <option value="60">60 menit setelah pesanan dibuat</option>
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Pesanan otomatis kedaluwarsa dan slot dilepas jika DP belum dibayar.</p>
                    </div>

                    <!-- Jam Operasional Klub -->
                    <div class="pt-5 border-t border-gray-100 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Jam Operasional Klub</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Menentukan rentang waktu pemilihan jam bermain di form booking & walk-in, serta display jam buka di halaman utama.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="open_hour" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Jam Buka <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="open_hour" name="open_hour"
                                       x-model="openHour"
                                       required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <p class="text-[11px] text-gray-400 mt-1">Format: HH:MM (Contoh: 10:00)</p>
                                @error('open_hour')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="close_hour" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Jam Tutup <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="close_hour" name="close_hour"
                                       x-model="closeHour"
                                       required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <p class="text-[11px] text-gray-400 mt-1">Format: HH:MM (Contoh: 23:00)</p>
                                @error('close_hour')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" @click="reset()"
                                class="px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                            Batalkan perubahan
                        </button>

                        <button type="submit" :disabled="isSubmitting" class="px-6 py-2.5 bg-[#1B5E43] hover:bg-[#154b35] disabled:bg-gray-400 disabled:opacity-75 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan pengaturan'"></span>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Card 2: Catatan Penerapan -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#1B5E43] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Catatan Penerapan Sistem</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Pesanan yang sudah dibuat sebelumnya tetap menggunakan besaran DP saat tanggal pemesanan. Sisa pembayaran wajib dilunasi kasir di lokasi sebelum bermain. Jika menggunakan nominal tetap, DP tidak akan melebihi total booking. Pengembalian dana atau kompensasi ditangani staf sesuai kebijakan klub.
                        </p>
                        <p class="text-[11px] text-gray-400 mt-3">
                            Terakhir diperbarui oleh <strong>{{ Auth::user()->name }}</strong> · {{ \Carbon\Carbon::now()->translatedFormat('d M Y, H.i') }} WIB
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Simulasi Perhitungan (4 cols) -->
        <div class="lg:col-span-4 space-y-5">
            
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-5 sticky top-24">
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Simulasi perhitungan</h3>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        <span x-text="dpType === 'fixed' ? 'DP ' + formatRupiah(dpNominal) + ' aktif' : 'DP ' + dpPct + '% aktif'"></span>
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Contoh Meja</label>
                        <select x-model.number="simPrice" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none">
                            <option value="80000">Meja Reguler · Rp80.000/jam</option>
                            <option value="120000">Meja VIP · Rp120.000/jam</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Durasi Bermain</label>
                        <select x-model.number="simDuration" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none">
                            <option value="1">1 jam</option>
                            <option value="2">2 jam</option>
                            <option value="3">3 jam</option>
                            <option value="4">4 jam</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2.5 pt-2 border-t border-gray-100 text-xs sm:text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Tarif per jam</span>
                        <span class="font-medium text-gray-900" x-text="formatRupiah(simPrice)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span x-text="'Total · ' + simDuration + ' × ' + formatRupiah(simPrice)"></span>
                        <span class="font-bold text-gray-900" x-text="formatRupiah(simTotal)"></span>
                    </div>
                </div>

                <!-- Highlight DP Box -->
                <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-5 text-center space-y-1">
                    <span class="text-xs font-bold text-[#1B5E43] uppercase tracking-wider block"
                          x-text="dpType === 'fixed' ? 'DP = Nominal tetap' : ('DP = ' + dpPct + '% × ' + formatRupiah(simTotal))">
                    </span>
                    <div class="text-3xl font-extrabold text-[#1B5E43] tracking-tight"
                         x-text="formatRupiah(simDp)">
                    </div>
                    <span class="text-[11px] text-gray-500 block pt-1">
                        Dibayar online saat booking
                    </span>
                </div>

                <div class="flex justify-between items-center text-xs font-medium text-gray-700 pt-1">
                    <span>Sisa pelunasan di lokasi</span>
                    <span class="font-bold text-gray-900" x-text="formatRupiah(simRemaining)"></span>
                </div>

                <p class="text-[11px] text-gray-400 leading-tight">
                    <template x-if="dpType === 'percentage'">
                        <span>Contoh VIP: 2 jam × Rp120.000 = Rp240.000. DP <span x-text="dpPct"></span>% = <span x-text="formatRupiah(240000 * (dpPct / 100))"></span>.</span>
                    </template>
                    <template x-if="dpType === 'fixed'">
                        <span>Contoh VIP: 2 jam × Rp120.000 = Rp240.000. DP = <span x-text="formatRupiah(Math.min(dpNominal, 240000))"></span> (Sisa pelunasan <span x-text="formatRupiah(Math.max(0, 240000 - Math.min(dpNominal, 240000)))"></span>).</span>
                    </template>
                </p>

            </div>

            <!-- Bottom Notification -->
            <div class="bg-emerald-50/60 border border-emerald-200/60 rounded-xl p-4 text-xs text-emerald-900 flex gap-2">
                <svg class="w-4 h-4 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Nilai aturan DP yang sama ditampilkan otomatis di ringkasan booking pelanggan, halaman pembayaran Snap, dan laporan admin.</span>
            </div>

        </div>

    </div>

</div>

<script>
    function settingsSimulator(initialType, initialDp, initialNominal, initialExpiry, initialOpenHour, initialCloseHour) {
        return {
            dpType: initialType || 'percentage',
            dpPct: initialDp || 30,
            dpNominal: initialNominal || 50000,
            dpExpiry: initialExpiry || 15,
            openHour: initialOpenHour || '10:00',
            closeHour: initialCloseHour || '23:00',
            showResetNotice: false,
            resetNoticeTimeout: null,
            isSubmitting: false,
            simPrice: 80000,
            simDuration: 2,

            get simTotal() {
                return this.simPrice * this.simDuration;
            },

            get simDp() {
                if (this.dpType === 'fixed') {
                    return Math.min(this.dpNominal || 0, this.simTotal);
                }
                return this.simTotal * ((this.dpPct || 0) / 100);
            },

            get simRemaining() {
                return Math.max(0, this.simTotal - this.simDp);
            },

            reset() {
                this.dpType = initialType || 'percentage';
                this.dpPct = initialDp || 30;
                this.dpNominal = initialNominal || 50000;
                this.dpExpiry = initialExpiry || 15;
                this.openHour = initialOpenHour || '10:00';
                this.closeHour = initialCloseHour || '23:00';

                this.showResetNotice = true;
                if (this.resetNoticeTimeout) clearTimeout(this.resetNoticeTimeout);
                this.resetNoticeTimeout = setTimeout(() => {
                    this.showResetNotice = false;
                }, 4000);
            },

            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(num || 0);
            }
        };
    }
</script>
@endsection
