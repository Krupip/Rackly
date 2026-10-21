@extends('layouts.admin')

@section('title', 'Dashboard — Admin Rackly Billiard')
@section('breadcrumb', 'Operasional / Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- Header Greeting & CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">RINGKASAN OPERASIONAL</span>
            @php
                $hour = (int) date('H');
                $greeting = 'Selamat pagi';
                if ($hour >= 11 && $hour < 15) $greeting = 'Selamat siang';
                elseif ($hour >= 15 && $hour < 18) $greeting = 'Selamat sore';
                elseif ($hour >= 18 || $hour < 4) $greeting = 'Selamat malam';
            @endphp
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                {{ $greeting }}, {{ Auth::user()->name }}.
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Ini aktivitas Rackly Billiard hari ini. Terakhir diperbarui pukul {{ \Carbon\Carbon::now()->format('H.i') }} WIB.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.walkin.create') }}" class="inline-flex items-center gap-2 bg-[#1B5E43] hover:bg-[#154b35] text-white px-5 py-3 rounded-xl font-bold text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah walk-in
            </a>
        </div>
    </div>

    <!-- 4 Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- 1. Booking Sukses -->
        <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Booking sukses</span>
                <div class="w-8 h-8 rounded-full bg-emerald-50 text-[#1B5E43] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 mb-2">
                {{ $bookingSuksesCount }}
            </div>
            <p class="text-xs text-gray-400">
                {{ $selesaiCount }} selesai · {{ $terkonfirmasiCount }} DP lunas
            </p>
        </div>

        <!-- 2. Pendapatan Diterima (Green Highlight Card) -->
        <div class="bg-[#1B5E43] text-white rounded-2xl p-6 shadow-md shadow-[#1B5E43]/20 hover:bg-[#164e3b] transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-emerald-200 uppercase tracking-wider">Pendapatan diterima</span>
                <div class="w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-white mb-2 tracking-tight">
                Rp{{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <p class="text-xs text-emerald-100">
                DP dan pelunasan diterima kasir & online
            </p>
        </div>

        <!-- 3. Menunggu Pembayaran -->
        <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Menunggu pembayaran</span>
                <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 mb-2">
                {{ $pendingCount }}
            </div>
            <p class="text-xs text-amber-600 font-medium">
                Total DP tertunda Rp{{ number_format($pendingDpTotal, 0, ',', '.') }}
            </p>
        </div>

        <!-- 4. Meja Siap Dipakai -->
        <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Meja siap dipakai</span>
                <div class="w-8 h-8 rounded-full bg-emerald-50 text-[#1B5E43] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16M10 4v16"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 mb-2">
                {{ $activeTablesCount }} / {{ $totalTablesCount }}
            </div>
            <p class="text-xs text-gray-400">
                Meja aktif & siap menerima pesanan
            </p>
        </div>

    </div>

    <!-- Middle Row: Operational Summary & Table Monitor -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Ringkasan & Operasional (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Aktivitas & Kinerja Operasional</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Ringkasan transaksi dan pemesanan terbaru di Rackly Billiard Club</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-[#1B5E43] border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B5E43]"></span>
                    Sistem Aktif
                </span>
            </div>

            <!-- Visual Bar Grid representation -->
            <div class="p-6 bg-gray-50/70 border border-gray-200/60 rounded-xl space-y-4">
                <div class="flex justify-between items-center text-xs font-semibold text-gray-600">
                    <span>Target Utilisasi Meja Harian</span>
                    <span class="text-[#1B5E43] font-bold">{{ $totalTablesCount > 0 ? round(($activeTablesCount / $totalTablesCount) * 100) : 0 }}% Kesiapan</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-[#1B5E43] h-3 rounded-full transition-all duration-500" style="width: {{ $totalTablesCount > 0 ? ($activeTablesCount / $totalTablesCount) * 100 : 0 }}%"></div>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-2 text-center text-xs border-t border-gray-200/60">
                    <div>
                        <span class="text-gray-400 block mb-0.5">Total Meja</span>
                        <strong class="text-gray-800 text-sm">{{ $totalTablesCount }} Unit</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Meja Aktif</span>
                        <strong class="text-emerald-700 text-sm">{{ $activeTablesCount }} Siap</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Perawatan</span>
                        <strong class="text-amber-600 text-sm">{{ $totalTablesCount - $activeTablesCount }} Meja</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Action Shortcuts -->
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="{{ route('admin.walkin.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-[#1B5E43] hover:bg-emerald-100 rounded-xl text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Catat Pelanggan Walk-in
                </a>
                <a href="{{ route('admin.bookings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Konfirmasi Pelunasan
                </a>
                <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Atur Persentase DP
                </a>
            </div>
        </div>

        <!-- Right: Pantauan Meja (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-base font-bold text-gray-900">Pantauan meja</h3>
                <span class="text-xs text-gray-400">Status saat ini</span>
            </div>

            <div class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto">
                @forelse($allTables as $table)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="text-sm font-bold text-gray-900 block">Meja {{ $table->table_number }}</span>
                            <span class="text-xs text-gray-400">{{ $table->type }} · Rp{{ number_format($table->price_per_hour, 0, ',', '.') }}/jam</span>
                        </div>
                        <div>
                            @if($table->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Tersedia
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    Perawatan
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-4 text-center">Belum ada meja terdaftar.</p>
                @endforelse
            </div>

            <div class="pt-2 border-t border-gray-100">
                <a href="{{ route('tables.index') }}" class="text-xs font-bold text-[#1B5E43] hover:underline flex items-center gap-1 justify-end">
                    Kelola semua meja &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Pesanan Terbaru Table -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Pesanan terbaru</h3>
                <p class="text-xs text-gray-400 mt-0.5">Daftar transaksi dan booking yang baru masuk ke sistem</p>
            </div>
            <a href="{{ route('admin.bookings') }}" class="text-xs font-bold text-[#1B5E43] hover:underline flex items-center gap-1">
                Lihat semua pesanan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Kode Pesanan</th>
                        <th class="py-3.5 px-6">Pelanggan</th>
                        <th class="py-3.5 px-6">Meja & Jadwal</th>
                        <th class="py-3.5 px-6">Total / Pembayaran</th>
                        <th class="py-3.5 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($recentBookings as $booking)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 px-6 font-mono font-bold text-gray-900 whitespace-nowrap">
                                #CP-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}<br>
                                <span class="text-[10px] font-normal text-gray-400">{{ $booking->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-bold text-gray-900 block">{{ $booking->is_walkin ? $booking->guest_name : ($booking->user->name ?? '-') }}</span>
                                <span class="text-[10px] text-gray-400">{{ $booking->is_walkin ? 'Walk-in Kasir' : 'Online Booking' }}</span>
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
                                        Terkonfirmasi (DP Lunas)
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-6 text-center text-gray-400">
                                Belum ada pesanan terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection