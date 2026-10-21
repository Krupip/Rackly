@extends('layouts.admin')

@section('title', 'Tambah Pesanan Walk-in — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Tambah pesanan walk-in')

@section('content')
<div x-data="walkInManager()" x-init="init()" class="space-y-8">
    
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
                </div>

                <!-- Card 2: Meja & Jadwal -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Meja & jadwal</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Periksa ketersediaan meja sebelum menyimpan pesanan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="billiard_table_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Pilih Meja <span class="text-red-500">*</span>
                            </label>
                            <select id="billiard_table_id" name="billiard_table_id" x-model="selectedTableId" @change="updateTableSelection($event.target)" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <option value="">-- Pilih Meja Biliar --</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}"
                                            data-number="{{ $table->table_number }}"
                                            data-type="{{ $table->type }}"
                                            data-price="{{ $table->price_per_hour }}">
                                        Meja {{ $table->table_number }} · {{ $table->type }} (Rp{{ number_format($table->price_per_hour, 0, ',', '.') }}/jam)
                                    </option>
                                @endforeach
                            </select>
                            @error('billiard_table_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="booking_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Tanggal Bermain <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="booking_date" name="booking_date" x-model="selectedDate" required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                        </div>
                    </div>

                    <!-- Jam Mulai & Jam Selesai -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <button type="button" @click="openStart = !openStart; openEnd = false"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 flex justify-between items-center text-left">
                                    <span x-text="startTime ? startTime + ' WIB' : '-- Jam Mulai --'"></span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openStart" @click.outside="openStart = false" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto py-1 text-xs">
                                    <template x-for="item in availableStartHours" :key="item">
                                        <button type="button" @click="startTime = item; openStart = false; endTime = '';"
                                                class="w-full px-4 py-2 text-left hover:bg-[#1B5E43] hover:text-white transition flex items-center justify-between font-semibold"
                                                :class="startTime === item ? 'bg-emerald-50 text-[#1B5E43]' : 'text-gray-700'">
                                            <span x-text="item + ' WIB'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="start_time" x-model="startTime" required>
                            @error('start_time')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <button type="button" @click="if(startTime){ openEnd = !openEnd; openStart = false; }"
                                        :disabled="!startTime"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 flex justify-between items-center text-left disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-text="endTime ? endTime + ' WIB' : (startTime ? '-- Jam Selesai --' : 'Pilih Jam Mulai Dulu')"></span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openEnd && startTime" @click.outside="openEnd = false" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto py-1 text-xs">
                                    <template x-for="item in availableEndHours" :key="item">
                                        <button type="button" @click="endTime = item; openEnd = false;"
                                                class="w-full px-4 py-2 text-left hover:bg-[#1B5E43] hover:text-white transition flex items-center justify-between font-semibold"
                                                :class="endTime === item ? 'bg-emerald-50 text-[#1B5E43]' : 'text-gray-700'">
                                            <span x-text="item + ' WIB'"></span>
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

                    <!-- Dynamic Availability Notice -->
                    <template x-if="selectedTableName && startTime && endTime">
                        <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-xl text-xs text-emerald-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#1B5E43] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>
                                <strong x-text="'Meja ' + selectedTableName"></strong> tersedia pada <strong x-text="startTime + ' – ' + endTime + ' WIB'"></strong>. Durasi <strong x-text="durationHours + ' jam'"></strong>, tarif <span x-text="formatRupiah(pricePerHour) + '/jam'"></span>.
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
                            <span class="font-bold text-gray-900" x-text="selectedTableName ? 'Meja ' + selectedTableName : '-'"></span>
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
    function walkInManager() {
        return {
            openStart: false,
            openEnd: false,
            customerName: '{{ old('guest_name') }}' || '',
            selectedTableId: '{{ old('billiard_table_id') }}' || '',
            selectedTableName: '',
            pricePerHour: 0,
            selectedDate: '{{ old('booking_date', date('Y-m-d')) }}',
            startTime: '{{ old('start_time') }}' || '',
            endTime: '{{ old('end_time') }}' || '',
            paymentMethod: 'Tunai',
            cashReceived: '',

            init() {
                this.updateTableSelection();
            },

            updateTableSelection(selectEl) {
                if (!selectEl) {
                    selectEl = document.getElementById('billiard_table_id');
                }
                if (selectEl && selectEl.selectedIndex >= 0) {
                    const opt = selectEl.options[selectEl.selectedIndex];
                    this.pricePerHour = opt && opt.dataset.price ? parseInt(opt.dataset.price) : 0;
                    this.selectedTableName = opt && opt.dataset.number ? opt.dataset.number + ' · ' + opt.dataset.type : '';
                }
            },

            get availableStartHours() {
                let hours = [];
                for (let i = 10; i <= 23; i++) {
                    hours.push(i.toString().padStart(2, '0') + ':00');
                }
                return hours;
            },

            get availableEndHours() {
                if (!this.startTime) return [];
                let start = parseInt(this.startTime.split(':')[0]);
                let hours = [];
                for (let i = start + 1; i <= 24; i++) {
                    hours.push((i === 24 ? '00:00' : i.toString().padStart(2, '0') + ':00'));
                }
                return hours;
            },

            get durationHours() {
                if (!this.startTime || !this.endTime) return 0;
                let s = parseInt(this.startTime.split(':')[0]);
                let e = parseInt(this.endTime.split(':')[0]);
                if (e === 0) e = 24; // midnight
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
                       this.durationHours > 0;
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
