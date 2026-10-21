@extends('layouts.admin')

@section('title', 'Kelola Pesanan — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Kelola pesanan')

@section('content')
<div class="space-y-8" x-data="{
    search: '',
    statusFilter: '',
    sourceFilter: '',
    selectedBooking: {{ $bookings->first() ? json_encode([
        'id' => $bookings->first()->id,
        'code' => '#CP-' . str_pad($bookings->first()->id, 6, '0', STR_PAD_LEFT),
        'customer' => $bookings->first()->is_walkin ? $bookings->first()->guest_name : ($bookings->first()->user->name ?? '-'),
        'email' => $bookings->first()->is_walkin ? '-' : ($bookings->first()->user->email ?? '-'),
        'source' => $bookings->first()->is_walkin ? 'Walk-in' : 'Online',
        'created_at' => $bookings->first()->created_at->format('d M Y, H:i') . ' WIB',
        'table' => ($bookings->first()->billiardTable->table_number ?? '-') . ' · ' . ($bookings->first()->billiardTable->type ?? 'Reguler'),
        'date' => \Carbon\Carbon::parse($bookings->first()->start_time)->translatedFormat('d M Y'),
        'time' => \Carbon\Carbon::parse($bookings->first()->start_time)->format('H.i') . ' – ' . \Carbon\Carbon::parse($bookings->first()->end_time)->format('H.i') . ' WIB',
        'duration' => \Carbon\Carbon::parse($bookings->first()->start_time)->diffInHours(\Carbon\Carbon::parse($bookings->first()->end_time)) . ' jam',
        'total' => 'Rp' . number_format($bookings->first()->total_price, 0, ',', '.'),
        'dp' => 'Rp' . number_format($bookings->first()->dp_amount, 0, ',', '.'),
        'remaining' => 'Rp' . number_format(max(0, $bookings->first()->total_price - $bookings->first()->dp_amount), 0, ',', '.'),
        'status' => $bookings->first()->status,
        'confirm_url' => route('admin.bookings.confirm', $bookings->first()->id)
    ]) : 'null' }}
}">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">OPERASIONAL / PESANAN</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Kelola pesanan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau jadwal, pembayaran, dan konfirmasi pelunasan setiap pesanan.</p>
        </div>

        <div>
            <a href="{{ route('admin.walkin.create') }}" class="inline-flex items-center gap-2 bg-[#1B5E43] hover:bg-[#154b35] text-white px-5 py-3 rounded-xl font-bold text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah walk-in
            </a>
        </div>
    </div>

    @php
        $totalOrders = $bookings->count();
        $suksesOrders = $bookings->whereIn('status', ['lunas', 'dp_lunas'])->count();
        $pendingOrders = $bookings->where('status', 'pending')->count();
        $batalOrders = $bookings->where('status', 'cancelled')->count();
    @endphp

    <!-- Metrics Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Total pesanan</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $totalOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Sukses</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#1B5E43]">{{ $suksesOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Menunggu DP</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $pendingOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Dibatalkan</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-red-600">{{ $batalOrders }}</div>
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
                    @forelse($bookings as $booking)
                        @php
                            $custName = $booking->is_walkin ? $booking->guest_name : ($booking->user->name ?? '-');
                            $custEmail = $booking->is_walkin ? '-' : ($booking->user->email ?? '-');
                            $src = $booking->is_walkin ? 'Walk-in' : 'Online';
                            $code = '#CP-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT);
                            $duration = \Carbon\Carbon::parse($booking->start_time)->diffInHours(\Carbon\Carbon::parse($booking->end_time)) . ' jam';
                            $remainingAmount = max(0, $booking->total_price - $booking->dp_amount);

                            $jsonData = json_encode([
                                'id' => $booking->id,
                                'code' => $code,
                                'customer' => $custName,
                                'email' => $custEmail,
                                'source' => $src,
                                'created_at' => $booking->created_at->format('d M Y, H:i') . ' WIB',
                                'table' => ($booking->billiardTable->table_number ?? '-') . ' · ' . ($booking->billiardTable->type ?? 'Reguler'),
                                'date' => \Carbon\Carbon::parse($booking->start_time)->translatedFormat('d M Y'),
                                'time' => \Carbon\Carbon::parse($booking->start_time)->format('H.i') . ' – ' . \Carbon\Carbon::parse($booking->end_time)->format('H.i') . ' WIB',
                                'duration' => $duration,
                                'total' => 'Rp' . number_format($booking->total_price, 0, ',', '.'),
                                'dp' => 'Rp' . number_format($booking->dp_amount, 0, ',', '.'),
                                'remaining' => 'Rp' . number_format($remainingAmount, 0, ',', '.'),
                                'status' => $booking->status,
                                'confirm_url' => route('admin.bookings.confirm', $booking->id)
                            ]);
                        @endphp
                        <tr class="hover:bg-emerald-50/30 cursor-pointer transition"
                            @click="selectedBooking = {{ $jsonData }}"
                            x-show="(
                                (search === '' || '{{ strtolower($custName) }}'.includes(search.toLowerCase()) || '{{ strtolower($code) }}'.includes(search.toLowerCase())) &&
                                (statusFilter === '' || statusFilter === '{{ $booking->status }}') &&
                                (sourceFilter === '' || (sourceFilter === 'walkin' && {{ $booking->is_walkin ? 'true' : 'false' }}) || (sourceFilter === 'online' && !{{ $booking->is_walkin ? 'true' : 'false' }}))
                            )">
                            <td class="py-4 px-6 font-mono font-bold text-gray-900 whitespace-nowrap">
                                {{ $code }}<br>
                                <span class="text-[10px] font-normal text-gray-400">{{ $booking->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-bold text-gray-900 block">{{ $custName }}</span>
                                <span class="text-[10px] text-gray-400">{{ $src }}</span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-semibold text-gray-800 block">Meja {{ $booking->billiardTable->table_number ?? '-' }} · {{ $booking->billiardTable->type ?? 'Reguler' }}</span>
                                <span class="text-[11px] text-gray-400">{{ \Carbon\Carbon::parse($booking->start_time)->format('H.i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H.i') }} WIB</span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-bold text-gray-900 block">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-gray-500">
                                    @if($booking->status === 'lunas')
                                        Lunas (Rp{{ number_format($booking->paid_amount, 0, ',', '.') }})
                                    @elseif($booking->status === 'dp_lunas')
                                        DP Rp{{ number_format($booking->dp_amount, 0, ',', '.') }}
                                    @else
                                        Belum bayar
                                    @endif
                                </span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($booking->status === 'lunas')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        Selesai
                                    </span>
                                @elseif($booking->status === 'dp_lunas')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        DP Lunas (Pelunasan Kasir)
                                    </span>
                                @elseif($booking->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu DP
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-800 border border-red-200">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap text-right">
                                @if($booking->status === 'dp_lunas')
                                    <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST"
                                          onsubmit="return confirm('Konfirmasi pelunasan sisa pembayaran Rp {{ number_format($remainingAmount, 0, ',', '.') }} untuk pesanan ini?');"
                                          class="inline-block" @click.stop>
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-lg text-xs font-bold transition shadow-sm">
                                            Konfirmasi Pelunasan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-xs">Pilih untuk detail &rarr;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-gray-400">
                                Belum ada pesanan terdaftar.
                            </td>
                        </tr>
                    @endforelse
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
                              'bg-red-50 text-red-800 border border-red-200': selectedBooking.status === 'cancelled'
                          }"
                          x-text="selectedBooking.status === 'dp_lunas' ? 'DP Lunas (Menunggu Pelunasan)' : (selectedBooking.status === 'lunas' ? 'Lunas / Selesai' : selectedBooking.status)">
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
@endsection
