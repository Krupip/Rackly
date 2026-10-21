<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BilliardTable;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Booking sukses (selesai & dp_lunas)
        $selesaiCount = Booking::where('status', 'lunas')->count();
        $terkonfirmasiCount = Booking::where('status', 'dp_lunas')->count();
        $bookingSuksesCount = $selesaiCount + $terkonfirmasiCount;

        // 2. Pendapatan diterima (paid_amount)
        $totalRevenue = Booking::whereIn('status', ['lunas', 'dp_lunas'])->sum('paid_amount');

        // 3. Menunggu pembayaran
        $pendingBookings = Booking::where('status', 'pending')->get();
        $pendingCount = $pendingBookings->count();
        $pendingDpTotal = $pendingBookings->sum('dp_amount');

        // 4. Meja
        $allTables = BilliardTable::all();
        $totalTablesCount = $allTables->count();
        $activeTablesCount = $allTables->where('is_active', 1)->count();

        // 5. Pesanan terbaru
        $recentBookings = Booking::with(['user', 'billiardTable'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'bookingSuksesCount',
            'selesaiCount',
            'terkonfirmasiCount',
            'totalRevenue',
            'pendingCount',
            'pendingDpTotal',
            'activeTablesCount',
            'totalTablesCount',
            'allTables',
            'recentBookings'
        ));
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
        if ($booking->status !== 'dp_lunas') {
            return redirect()->route('admin.bookings')
                ->with('error', 'Hanya pesanan berstatus "DP Lunas" yang bisa dikonfirmasi pelunasannya.');
        }

        $sisaPembayaran = $booking->total_price - $booking->dp_amount;

        $booking->update([
            'status'      => 'lunas',
            'paid_amount' => $booking->total_price,
        ]);

        return redirect()->route('admin.bookings')
            ->with('success', "Pelunasan untuk Booking #{$booking->id} berhasil dikonfirmasi. Sisa Rp " . number_format($sisaPembayaran, 0, ',', '.') . " telah diterima.");
    }

    // Tampilkan form tambah pesanan walk-in
    public function createWalkIn()
    {
        $tables = BilliardTable::where('is_active', 1)->get();
        return view('admin.walkin-create', compact('tables'));
    }

    // Simpan pesanan walk-in langsung di kasir
    public function storeWalkIn(Request $request)
    {
        $request->validate([
            'guest_name'        => 'required|string|max:255',
            'billiard_table_id' => 'required|exists:billiard_tables,id',
            'booking_date'      => 'required|date',
            'start_time'        => 'required|date_format:H:i',
            'end_time'          => 'required|date_format:H:i|after:start_time',
        ], [
            'guest_name.required'        => 'Nama tamu wajib diisi.',
            'billiard_table_id.required' => 'Pilih meja terlebih dahulu.',
            'end_time.after'             => 'Jam selesai harus setelah jam mulai.',
        ]);

        $table = BilliardTable::findOrFail($request->billiard_table_id);

        $start = Carbon::parse($request->booking_date . ' ' . $request->start_time);
        $end   = Carbon::parse($request->booking_date . ' ' . $request->end_time);

        // Cek jadwal bentrok
        $isConflict = Booking::where('billiard_table_id', $table->id)
            ->whereIn('status', ['pending', 'dp_lunas', 'lunas'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })->exists();

        if ($isConflict) {
            return back()->with('error', 'Jadwal ini sudah dipesan. Silakan pilih jam lain.')->withInput();
        }

        $durationHours = $start->diffInMinutes($end) / 60;
        $totalPrice    = $durationHours * $table->price_per_hour;

        // Walk-in langsung lunas — tidak perlu DP
        Booking::create([
            'user_id'           => null,
            'guest_name'        => $request->guest_name,
            'is_walkin'         => true,
            'billiard_table_id' => $table->id,
            'start_time'        => $start,
            'end_time'          => $end,
            'total_price'       => $totalPrice,
            'dp_amount'         => 0,
            'paid_amount'       => $totalPrice,
            'status'            => 'lunas',
        ]);

        return redirect()->route('admin.bookings')
            ->with('success', "Pesanan walk-in untuk \"{$request->guest_name}\" berhasil ditambahkan.");
    }
}