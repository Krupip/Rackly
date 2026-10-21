@extends('layouts.admin')

@section('title', 'Pengaturan Uang Muka — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Pengaturan DP')

@section('content')
@php
    $currentDp = (int) ($settings->get('dp_percentage')?->value ?? 30);
@endphp

<div x-data="settingsSimulator({{ $currentDp }})" class="space-y-8">
    
    <!-- Header -->
    <div>
        <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">KONFIGURASI / PEMBAYARAN</span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan uang muka</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Tentukan DP booking online. Transparan untuk pelanggan, terkontrol untuk klub.</p>
    </div>

    <!-- Green Info Banner -->
    <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 flex gap-3 text-xs text-emerald-900 leading-relaxed">
        <svg class="w-5 h-5 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            Aturan aktif: <strong>DP <span x-text="dpPct"></span>% dari total booking</strong>. Berlaku untuk seluruh meja Reguler dan VIP. Pesanan walk-in dibayar penuh di kasir tanpa DP.
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

                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Wajibkan DP Toggle Row -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-100 rounded-xl">
                        <div>
                            <span class="text-sm font-bold text-gray-900 block">Wajibkan DP untuk booking online</span>
                            <span class="text-xs text-gray-400">Jadwal dikonfirmasi setelah pembayaran DP berhasil.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked disabled class="sr-only peer">
                            <div class="w-11 h-6 bg-[#1B5E43] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                    </div>

                    <!-- Metode Perhitungan DP Selection -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Metode Perhitungan DP</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="border-2 border-[#1B5E43] bg-emerald-50/40 rounded-xl p-4 cursor-pointer">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-3 h-3 rounded-full bg-[#1B5E43]"></span>
                                    <span class="text-sm font-bold text-[#1B5E43]">Persentase</span>
                                </div>
                                <p class="text-xs text-gray-500">Dihitung otomatis dari durasi dan total harga booking</p>
                            </div>

                            <div class="border border-gray-200 bg-gray-50/60 rounded-xl p-4 opacity-60 cursor-not-allowed">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                                    <span class="text-sm font-bold text-gray-500">Nominal Tetap</span>
                                </div>
                                <p class="text-xs text-gray-400">Nilai rupiah tetap per pesanan (saat ini dinonaktifkan)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Input Persentase DP & Nominal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="dp_percentage" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Persentase DP Aktif <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="dp_percentage" name="dp_percentage"
                                       x-model.number="dpPct" min="1" max="100" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-base font-extrabold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition pr-10">
                                <span class="absolute right-4 top-3.5 text-sm font-bold text-gray-400">%</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Rentang 1–100% dari total booking.</p>
                            @error('dp_percentage')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                Nominal DP (tidak aktif)
                            </label>
                            <input type="text" disabled value="Rp80.000"
                                   class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm font-medium text-gray-400 cursor-not-allowed">
                            <p class="text-[11px] text-gray-400 mt-1">Digunakan jika memilih Nominal tetap.</p>
                        </div>
                    </div>

                    <!-- Batas Waktu Pembayaran DP -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Batas Waktu Pembayaran DP
                        </label>
                        <select class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                            <option value="15">15 menit setelah pesanan dibuat (Standar)</option>
                            <option value="30">30 menit setelah pesanan dibuat</option>
                            <option value="60">60 menit setelah pesanan dibuat</option>
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Pesanan otomatis kedaluwarsa dan slot dilepas jika DP belum dibayar.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" @click="dpPct = {{ $currentDp }}"
                                class="px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                            Batalkan perubahan
                        </button>

                        <button type="submit" class="px-6 py-2.5 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan pengaturan
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
                            Pesanan yang sudah dibuat sebelumnya tetap menggunakan besaran DP saat tanggal pemesanan. Sisa pembayaran wajib dilunasi kasir di lokasi sebelum bermain. Pengembalian dana atau kompensasi ditangani staf sesuai kebijakan klub.
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
                        DP <span x-text="dpPct"></span>% aktif
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Contoh Meja</label>
                        <select x-model="simPrice" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none">
                            <option value="80000">Meja Reguler · Rp80.000/jam</option>
                            <option value="120000">Meja VIP · Rp120.000/jam</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Durasi Bermain</label>
                        <select x-model="simDuration" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:outline-none">
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
                          x-text="'DP = ' + dpPct + '% × ' + formatRupiah(simTotal)">
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
                    Contoh VIP: 2 jam × Rp120.000 = Rp240.000. DP <span x-text="dpPct"></span>% = <span x-text="formatRupiah(240000 * (dpPct / 100))"></span>.
                </p>

            </div>

            <!-- Bottom Notification -->
            <div class="bg-emerald-50/60 border border-emerald-200/60 rounded-xl p-4 text-xs text-emerald-900 flex gap-2">
                <svg class="w-4 h-4 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Nilai persentase DP yang sama ditampilkan otomatis di ringkasan booking pelanggan, halaman pembayaran Snap, dan laporan admin.</span>
            </div>

        </div>

    </div>

</div>

<script>
    function settingsSimulator(initialDp) {
        return {
            dpPct: initialDp,
            simPrice: 80000,
            simDuration: 2,

            get simTotal() {
                return this.simPrice * this.simDuration;
            },

            get simDp() {
                return this.simTotal * (this.dpPct / 100);
            },

            get simRemaining() {
                return this.simTotal - this.simDp;
            },

            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(num);
            }
        };
    }
</script>
@endsection
