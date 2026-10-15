@extends('layouts.admin')

@section('title', 'Tambah Pesanan Walk-in - Admin Rackly')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Pesanan Walk-in</h1>
            <p class="text-gray-500 mt-1">Catat pesanan untuk pelanggan yang datang langsung ke lokasi. Status langsung <strong>Lunas</strong>.</p>
        </div>
        <a href="{{ route('admin.bookings') }}" class="text-sm text-gray-500 hover:text-gray-800 transition">
            ← Kembali ke Kelola Pesanan
        </a>
    </div>

    {{-- Flash error --}}
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8"
        x-data="walkInCalculator()"
        x-init="init()">

        {{-- Form Input --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow p-8">
                <form action="{{ route('admin.walkin.store') }}" method="POST" id="walkin-form">
                    @csrf

                    {{-- Nama Tamu --}}
                    <div class="mb-6">
                        <label for="guest_name" class="block text-sm font-bold text-gray-700 mb-1">
                            Nama Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="guest_name" name="guest_name"
                            value="{{ old('guest_name') }}"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('guest_name') border-red-500 @enderror">
                        @error('guest_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilih Meja --}}
                    <div class="mb-6">
                        <label for="billiard_table_id" class="block text-sm font-bold text-gray-700 mb-1">
                            Pilih Meja <span class="text-red-500">*</span>
                        </label>
                        <select id="billiard_table_id" name="billiard_table_id"
                            x-on:change="updatePrice($event.target)"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('billiard_table_id') border-red-500 @enderror">
                            <option value="">-- Pilih Meja --</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}"
                                    data-price="{{ $table->price_per_hour }}"
                                    {{ old('billiard_table_id') == $table->id ? 'selected' : '' }}>
                                    {{ $table->table_number }} — {{ $table->type }} (Rp {{ number_format($table->price_per_hour, 0, ',', '.') }}/jam)
                                </option>
                            @endforeach
                        </select>
                        @error('billiard_table_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-6">
                        <label for="booking_date" class="block text-sm font-bold text-gray-700 mb-1">
                            Tanggal Main <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="booking_date" name="booking_date"
                            x-model="selectedDate"
                            x-on:change="startTime = ''; endTime = ''"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Jam Mulai & Selesai (Dropdown Alpine.js) --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        {{-- Jam Mulai --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <button type="button"
                                    @click="openStart = !openStart; openEnd = false"
                                    @click.outside="openStart = false"
                                    class="shadow border rounded-lg w-full py-2.5 px-3 text-left text-gray-700 bg-white flex justify-between items-center focus:ring-2 focus:ring-blue-500">
                                    <span x-text="startTime ? startTime : '-- Jam Mulai --'">-- Jam Mulai --</span>
                                    <svg class="h-4 w-4 text-gray-500 transition-transform duration-200" :class="openStart ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <ul x-show="openStart" style="display:none"
                                    class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto py-1 text-sm">
                                    <template x-for="item in availableStartHours" :key="item.time">
                                        <li @click="if(!item.isDisabled){ startTime = item.time; openStart = false; endTime = ''; }"
                                            class="select-none relative py-2 px-3"
                                            :class="{
                                                'cursor-not-allowed text-gray-400 bg-gray-50': item.isDisabled,
                                                'cursor-pointer hover:bg-blue-600 hover:text-white text-gray-900': !item.isDisabled,
                                                'bg-blue-50 text-blue-700 font-semibold': startTime === item.time && !item.isDisabled
                                            }"
                                            x-text="item.time">
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            <input type="hidden" name="start_time" x-model="startTime">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jam Selesai --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <button type="button"
                                    @click="if(startTime){ openEnd = !openEnd; openStart = false; }"
                                    @click.outside="openEnd = false"
                                    :disabled="!startTime"
                                    class="shadow border rounded-lg w-full py-2.5 px-3 text-left text-gray-700 bg-white flex justify-between items-center focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <span x-text="endTime ? endTime : (startTime ? '-- Jam Selesai --' : 'Pilih Jam Mulai Dulu')">Pilih Jam Mulai Dulu</span>
                                    <svg class="h-4 w-4 text-gray-500 transition-transform duration-200" :class="openEnd ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <ul x-show="openEnd && startTime" style="display:none"
                                    class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto py-1 text-sm">
                                    <template x-for="hour in availableEndHours" :key="hour">
                                        <li @click="endTime = hour; openEnd = false"
                                            class="cursor-pointer select-none relative py-2 px-3 hover:bg-blue-600 hover:text-white"
                                            :class="endTime === hour ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-900'"
                                            x-text="hour">
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            <input type="hidden" name="end_time" x-model="endTime">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" :disabled="!isValid"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-lg transition shadow">
                        ✓ Simpan & Konfirmasi Lunas
                    </button>
                </form>
            </div>
        </div>

        {{-- Ringkasan Harga --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow p-6 sticky top-8">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Durasi</span>
                        <span x-text="hours > 0 ? hours + ' jam' : '-'" class="font-medium">-</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Harga/jam</span>
                        <span x-text="pricePerHour > 0 ? formatRupiah(pricePerHour) : '-'" class="font-medium">-</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between text-base font-bold text-green-700">
                        <span>Total Dibayar</span>
                        <span x-text="formatRupiah(totalPrice)">Rp 0</span>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200 text-xs text-green-700">
                    <strong>Walk-in:</strong> Tidak ada DP. Pelanggan langsung membayar penuh di kasir.
                </div>
            </div>
        </div>

    </div>

    <script>
        function walkInCalculator() {
            return {
                openStart: false,
                openEnd: false,
                pricePerHour: 0,
                selectedDate: '{{ old('booking_date') }}' || '',
                today: '',
                currentHour: new Date().getHours(),
                startTime: '{{ old('start_time') }}' || '',
                endTime: '{{ old('end_time') }}' || '',

                init() {
                    const d = new Date();
                    const dateStr = d.getFullYear() + '-'
                        + String(d.getMonth() + 1).padStart(2, '0') + '-'
                        + String(d.getDate()).padStart(2, '0');
                    this.today = dateStr;
                    if (!this.selectedDate) {
                        this.selectedDate = dateStr;
                    }

                    this.updatePrice();
                },

                updatePrice(select) {
                    if (!select || !select.options) {
                        select = document.getElementById('billiard_table_id');
                    }
                    if (select && select.selectedIndex >= 0) {
                        const opt = select.options[select.selectedIndex];
                        this.pricePerHour = opt && opt.dataset.price ? parseInt(opt.dataset.price) : 0;
                    }
                },

                get availableStartHours() {
                    let hours = [];
                    for (let i = 8; i <= 22; i++) {
                        hours.push({
                            time: i.toString().padStart(2, '0') + ':00',
                            isDisabled: false
                        });
                    }
                    return hours;
                },

                get availableEndHours() {
                    if (!this.startTime) return [];
                    let start = parseInt(this.startTime.split(':')[0]);
                    let hours = [];
                    for (let i = start + 1; i <= 23; i++) {
                        hours.push(i.toString().padStart(2, '0') + ':00');
                    }
                    return hours;
                },

                get hours() {
                    if (!this.startTime || !this.endTime) return 0;
                    const s = parseInt(this.startTime.split(':')[0]);
                    const e = parseInt(this.endTime.split(':')[0]);
                    return e > s ? e - s : 0;
                },

                get totalPrice() {
                    return this.hours * this.pricePerHour;
                },

                get isValid() {
                    return this.hours > 0 && this.pricePerHour > 0;
                },

                formatRupiah(n) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                    }).format(n);
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('walkInCalculator', walkInCalculator);
        });
    </script>
@endsection
