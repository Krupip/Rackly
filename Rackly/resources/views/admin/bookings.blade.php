@extends('layouts.admin')

@section('title', 'Kelola Pesanan - Admin Rackly')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Pesanan</h1>
    <p class="text-gray-500 mb-8">Konfirmasi pelunasan sisa pembayaran dari pelanggan yang sudah hadir.</p>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemesan</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meja / Jadwal</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Total</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">DP Dibayar</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Tagihan</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 text-sm text-gray-600">#{{ $booking->id }}</td>
                        <td class="px-5 py-4">
                            @if($booking->is_walkin)
                                <div class="text-sm font-medium text-gray-900">{{ $booking->guest_name }}</div>
                                <span class="inline-flex text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-semibold">Walk-in</span>
                            @else
                                <div class="text-sm font-medium text-gray-900">{{ $booking->user->name ?? 'User Dihapus' }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->user->email ?? '-' }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-sm font-bold text-blue-600">{{ $booking->billiardTable->table_number ?? '-' }}</div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }},
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm font-medium text-gray-900">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">
                            Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-sm font-semibold {{ $booking->status === 'dp_lunas' ? 'text-orange-600' : 'text-gray-400' }}">
                            @if($booking->status === 'dp_lunas')
                                Rp {{ number_format($booking->total_price - $booking->dp_amount, 0, ',', '.') }}
                            @elseif($booking->status === 'lunas')
                                <span class="text-green-600">Lunas</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($booking->status === 'pending')
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu DP</span>
                            @elseif($booking->status === 'dp_lunas')
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">DP Lunas</span>
                            @elseif($booking->status === 'lunas')
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($booking->status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($booking->status === 'dp_lunas')
                                @php $sisa = number_format($booking->total_price - $booking->dp_amount, 0, ',', '.'); @endphp
                                <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST"
                                    data-confirm="Konfirmasi pelunasan sisa Rp {{ $sisa }} dari {{ $booking->user->name ?? 'user' }}?"
                                    onsubmit="return confirm(this.dataset.confirm)">
                                    @csrf
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                        ✓ Konfirmasi Lunas
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
