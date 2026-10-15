<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class AdminController extends Controller
{
    public function index()
    {
        // Menghitung total pesanan yang berstatus 'lunas' (sudah bayar penuh)
        $totalPaidBookings = Booking::where('status', 'lunas')->count();

        // Menghitung total pendapatan dari pesanan 'lunas' (pakai paid_amount)
        $totalRevenue = Booking::where('status', 'lunas')->sum('paid_amount');

        // Mengambil 10 pesanan terbaru
        $recentBookings = Booking::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard', compact('totalPaidBookings', 'totalRevenue', 'recentBookings'));
    }

    // Halaman kelola semua pesanan (untuk proses pelunasan)
    public function bookings()
    {
        $bookings = Booking::with(['user', 'billiardTable'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bookings', compact('bookings'));
    }

    // Konfirmasi pelunasan sisa pembayaran oleh kasir/admin
    public function confirmFullPayment(Booking $booking)
    {
        // Hanya booking berstatus dp_lunas yang bisa dikonfirmasi
        if ($booking->status !== 'dp_lunas') {
            return redirect()->route('admin.bookings')
                ->with('error', 'Hanya pesanan berstatus "DP Lunas" yang bisa dikonfirmasi pelunasannya.');
        }

        $sisaPembayaran = $booking->total_price - $booking->dp_amount;

        $booking->update([
            'status'      => 'lunas',
            'paid_amount' => $booking->total_price, // Sudah bayar penuh
        ]);

        return redirect()->route('admin.bookings')
            ->with('success', "Pelunasan untuk Booking #{$booking->id} berhasil dikonfirmasi. Sisa Rp " . number_format($sisaPembayaran, 0, ',', '.') . " telah diterima.");
    }
}