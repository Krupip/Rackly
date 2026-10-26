@extends('layouts.admin')

@section('title', 'Kelola Pesanan — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Kelola pesanan')

@section('content')
@php
    $initialOrders = $bookings->map(function ($b) {
        $custName = $b->is_walkin ? $b->guest_name : ($b->user->name ?? '-');
        $custEmail = $b->is_walkin ? '-' : ($b->user->email ?? '-');
        $src = $b->is_walkin ? 'Walk-in' : 'Online';
        $code = '#CP-' . str_pad($b->id, 6, '0', STR_PAD_LEFT);
        $duration = \Carbon\Carbon::parse($b->start_time)->diffInHours(\Carbon\Carbon::parse($b->end_time)) . ' jam';
        $remainingAmount = max(0, $b->total_price - $b->dp_amount);

        return [
            'id' => $b->id,
            'code' => $code,
            'customer' => $custName,
            'email' => $custEmail,
            'source' => $src,
            'is_walkin' => (bool) $b->is_walkin,
            'created_at' => $b->created_at->format('d M Y, H:i') . ' WIB',
            'created_at_date' => $b->created_at->format('d M Y'),
            'table' => ($b->billiardTable->table_number ?? '-') . ' · ' . ($b->billiardTable->type ?? 'Reguler'),
            'table_name' => 'Meja ' . ($b->billiardTable->table_number ?? '-') . ' · ' . ($b->billiardTable->type ?? 'Reguler'),
            'date' => \Carbon\Carbon::parse($b->start_time)->translatedFormat('d M Y'),
            'time' => \Carbon\Carbon::parse($b->start_time)->format('H.i') . ' – ' . \Carbon\Carbon::parse($b->end_time)->format('H.i') . ' WIB',
            'duration' => $duration,
            'total' => 'Rp' . number_format($b->total_price, 0, ',', '.'),
            'dp' => 'Rp' . number_format($b->dp_amount, 0, ',', '.'),
            'remaining' => 'Rp' . number_format($remainingAmount, 0, ',', '.'),
            'total_formatted' => 'Rp' . number_format($b->total_price, 0, ',', '.'),
            'dp_formatted' => 'Rp' . number_format($b->dp_amount, 0, ',', '.'),
            'paid_formatted' => 'Rp' . number_format($b->paid_amount, 0, ',', '.'),
            'remaining_formatted' => 'Rp' . number_format($remainingAmount, 0, ',', '.'),
            'status' => $b->status,
            'confirm_url' => route('admin.bookings.confirm', $b->id),
        ];
    });

    $totalOrders = $bookings->count();
    $suksesOrders = $bookings->whereIn('status', ['lunas', 'dp_lunas'])->count();
    $pendingOrders = $bookings->where('status', 'pending')->count();
    $noShowOrders = $bookings->where('status', 'no_show')->count();
    $batalOrders = $bookings->where('status', 'cancelled')->count();

    $initialMetrics = [
        'totalOrders' => $totalOrders,
        'suksesOrders' => $suksesOrders,
        'pendingOrders' => $pendingOrders,
        'noShowOrders' => $noShowOrders,
        'batalOrders' => $batalOrders,
    ];
@endphp

<div class="space-y-8" x-data="adminBookingsManager({{ json_encode(['orders' => $initialOrders, 'metrics' => $initialMetrics]) }})" x-init="initRealtime()">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block">OPERASIONAL / PESANAN</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                    Live Real-time
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Kelola pesanan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau jadwal, pembayaran, dan konfirmasi pelunasan setiap pesanan secara langsung.</p>
        </div>

        <div>
            <a href="{{ route('admin.walkin.create') }}" class="inline-flex items-center gap-2 bg-[#1B5E43] hover:bg-[#154b35] text-white px-5 py-3 rounded-xl font-bold text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah walk-in
            </a>
        </div>
    </div>

    <!-- Metrics Stats Row -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Total pesanan</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-gray-900" x-text="metrics.totalOrders">{{ $totalOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Sukses</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#1B5E43]" x-text="metrics.suksesOrders">{{ $suksesOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Menunggu DP</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-600" x-text="metrics.pendingOrders">{{ $pendingOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">No-Show</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-rose-600" x-text="metrics.noShowOrders">{{ $noShowOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Dibatalkan</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-red-600" x-text="metrics.batalOrders">{{ $batalOrders }}</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" x-model="search" placeholder="Nama pelanggan atau kode..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Filter Status -->
            <select x-model="statusFilter" class="px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                <option value="">Semua status</option>
                <option value="dp_lunas">DP Lunas (Terkonfirmasi)</option>
                <option value="lunas">Lunas (Selesai)</option>
                <option value="pending">Menunggu DP</option>
                <option value="no_show">No-Show</option>
                <option value="cancelled">Dibatalkan</option>
            </select>

            <!-- Filter Sumber -->
            <select x-model="sourceFilter" class="px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                <option value="">Semua sumber</option>
                <option value="online">Online</option>
                <option value="walkin">Walk-in</option>
            </select>
        </div>

    </div>

    <!-- Table of Orders -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Kode Pesanan</th>
                        <th class="py-3.5 px-6">Pelanggan</th>
                        <th class="py-3.5 px-6">Meja & Jadwal</th>
                        <th class="py-3.5 px-6">Total / Pembayaran</th>
                        <th class="py-3.5 px-6">Status Pesanan</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <template x-for="booking in filteredOrders" :key="booking.id">
                        <tr class="hover:bg-emerald-50/30 cursor-pointer transition"
                            :class="selectedBooking && selectedBooking.id === booking.id ? 'bg-emerald-50/40' : ''"
                            @click="selectOrder(booking)">
                            <td class="py-4 px-6 font-mono font-bold text-gray-900 whitespace-nowrap">
                                <span x-text="booking.code"></span><br>
                                <span class="text-[10px] font-normal text-gray-400" x-text="booking.created_at_date"></span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-bold text-gray-900 block" x-text="booking.customer"></span>
                                <span class="text-[10px] text-gray-400" x-text="booking.source"></span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-semibold text-gray-800 block" x-text="booking.table_name"></span>
                                <span class="text-[11px] text-gray-400" x-text="booking.time"></span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-bold text-gray-900 block" x-text="booking.total_formatted"></span>
                                <span class="text-[10px] text-gray-500">
                                    <template x-if="booking.status === 'lunas'">
                                        <span x-text="'Lunas (' + booking.paid_formatted + ')'"></span>
                                    </template>
                                    <template x-if="booking.status === 'dp_lunas'">
                                        <span x-text="'DP ' + booking.dp_formatted"></span>
                                    </template>
                                    <template x-if="booking.status === 'pending'">
                                        <span>Belum bayar</span>
                                    </template>
                                    <template x-if="booking.status === 'no_show'">
                                        <span class="text-rose-600 font-semibold">No-Show</span>
                                    </template>
                                    <template x-if="booking.status === 'cancelled'">
                                        <span class="text-red-500">Dibatalkan</span>
                                    </template>
                                </span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <template x-if="booking.status === 'lunas'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        Selesai
                                    </span>
                                </template>
                                <template x-if="booking.status === 'dp_lunas'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        DP Lunas (Pelunasan Kasir)
                                    </span>
                                </template>
                                <template x-if="booking.status === 'pending'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu DP
                                    </span>
                                </template>
                                <template x-if="booking.status === 'no_show'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        No-Show
                                    </span>
                                </template>
                                <template x-if="booking.status === 'cancelled'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-800 border border-red-200">
                                        Dibatalkan
                                    </span>
                                </template>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap text-right">
                                <template x-if="booking.status === 'dp_lunas'">
                                    <form :action="booking.confirm_url" method="POST"
                                          @submit="return confirm('Konfirmasi pelunasan sisa pembayaran ' + booking.remaining_formatted + ' untuk pesanan ini?');"
                                          class="inline-block" @click.stop>
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-lg text-xs font-bold transition shadow-sm">
                                            Konfirmasi Pelunasan
                                        </button>
                                    </form>
                                </template>
                                <template x-if="booking.status !== 'dp_lunas'">
                                    <span class="text-gray-400 text-xs">Pilih untuk detail &rarr;</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredOrders.length === 0">
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-gray-400">
                                Tidak ada pesanan yang sesuai dengan filter atau pencarian.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Pesanan Terpilih (Interactive Bottom Panel as shown in reference) -->
    <template x-if="selectedBooking">
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">DETAIL PESANAN TERPILIH</span>
                    <h2 class="text-xl font-extrabold text-gray-900 font-mono" x-text="selectedBooking.code"></h2>
                </div>
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                          :class="{
                              'bg-emerald-50 text-emerald-800 border border-emerald-200': selectedBooking.status === 'dp_lunas',
                              'bg-gray-100 text-gray-700 border border-gray-200': selectedBooking.status === 'lunas',
                              'bg-amber-50 text-amber-800 border border-amber-200': selectedBooking.status === 'pending',
                              'bg-rose-50 text-rose-800 border border-rose-200': selectedBooking.status === 'no_show',
                              'bg-red-50 text-red-800 border border-red-200': selectedBooking.status === 'cancelled'
                          }"
                          x-text="selectedBooking.status === 'dp_lunas' ? 'DP Lunas (Menunggu Pelunasan)' : (selectedBooking.status === 'lunas' ? 'Lunas / Selesai' : (selectedBooking.status === 'no_show' ? 'No-Show (Melewati Jam Selesai)' : selectedBooking.status))">
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs sm:text-sm">
                <!-- Pelanggan -->
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">PELANGGAN</span>
                    <p class="font-extrabold text-gray-900 text-base" x-text="selectedBooking.customer"></p>
                    <p class="text-gray-500" x-text="selectedBooking.email"></p>
                    <p class="text-gray-400 text-xs">
                        Sumber: <strong class="text-gray-700" x-text="selectedBooking.source"></strong> · <span x-text="selectedBooking.created_at"></span>
                    </p>
                </div>

                <!-- Jadwal Bermain -->
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">JADWAL BERMAIN</span>
                    <div class="flex justify-between text-gray-600">
                        <span>Meja</span>
                        <strong class="text-gray-900" x-text="selectedBooking.table"></strong>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tanggal</span>
                        <strong class="text-gray-900" x-text="selectedBooking.date"></strong>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Jam</span>
                        <strong class="text-gray-900" x-text="selectedBooking.time"></strong>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Durasi</span>
                        <strong class="text-gray-900" x-text="selectedBooking.duration"></strong>
                    </div>
                </div>

                <!-- Pembayaran -->
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">PEMBAYARAN</span>
                    <div class="flex justify-between text-gray-600">
                        <span>Total</span>
                        <strong class="text-gray-900" x-text="selectedBooking.total"></strong>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>DP Terbayar</span>
                        <strong class="text-[#1B5E43]" x-text="selectedBooking.dp"></strong>
                    </div>
                    <div class="flex justify-between text-gray-600 border-t border-dashed border-gray-200 pt-1">
                        <span>Sisa di lokasi</span>
                        <strong class="text-gray-900 font-bold" x-text="selectedBooking.remaining"></strong>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-xs text-gray-400">
                    Periksa pembayaran tunai/kasir sebelum menekan konfirmasi pelunasan.
                </span>

                <div class="flex items-center gap-3">
                    <template x-if="selectedBooking.status === 'dp_lunas'">
                        <form :action="selectedBooking.confirm_url" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin sisa pembayaran sudah diterima dan pesanan ini lunas?');">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Konfirmasi Pelunasan & Selesaikan
                            </button>
                        </form>
                    </template>
                </div>
            </div>

        </div>
    </template>

</div>

<script>
    function adminBookingsManager(initialData) {
        return {
            search: '',
            statusFilter: '',
            sourceFilter: '',
            orders: initialData.orders || [],
            metrics: initialData.metrics || {},
            selectedBooking: initialData.orders && initialData.orders.length > 0 ? initialData.orders[0] : null,
            pollingTimer: null,

            initRealtime() {
                this.pollingTimer = setInterval(() => {
                    this.fetchBookings();
                }, 3000);
            },

            get filteredOrders() {
                return this.orders.filter(booking => {
                    const s = (this.search || '').toLowerCase().trim();
                    const matchSearch = !s ||
                        (booking.customer && booking.customer.toLowerCase().includes(s)) ||
                        (booking.code && booking.code.toLowerCase().includes(s));

                    const matchStatus = !this.statusFilter || booking.status === this.statusFilter;

                    const matchSource = !this.sourceFilter ||
                        (this.sourceFilter === 'walkin' && booking.is_walkin) ||
                        (this.sourceFilter === 'online' && !booking.is_walkin);

                    return matchSearch && matchStatus && matchSource;
                });
            },

            selectOrder(order) {
                this.selectedBooking = order;
            },

            async fetchBookings() {
                try {
                    const res = await fetch('{{ route('admin.realtime.bookings') }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data && data.status === 'success') {
                            this.orders = data.orders;
                            this.metrics = data.metrics;

                            if (this.selectedBooking) {
                                const updated = this.orders.find(o => o.id === this.selectedBooking.id);
                                if (updated) {
                                    this.selectedBooking = updated;
                                }
                            } else if (this.orders.length > 0) {
                                this.selectedBooking = this.orders[0];
                            }
                        }
                    }
                } catch (e) {
                    // Fail silently on network errors
                }
            }
        };
    }
</script>
@endsection
