<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BilliardTable;
use App\Models\Setting;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Otomatis ubah pesanan DP lunas yang sudah melewati jam selesai menjadi no_show
        Booking::where('status', 'dp_lunas')
            ->where('end_time', '<', Carbon::now())
            ->update(['status' => 'no_show']);

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
        // Otomatis ubah pesanan DP lunas yang sudah melewati jam selesai menjadi no_show
        Booking::where('status', 'dp_lunas')
            ->where('end_time', '<', Carbon::now())
            ->update(['status' => 'no_show']);

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
        $openHour = Setting::get('open_hour', '10:00');
        $closeHour = Setting::get('close_hour', '23:00');

        $existingBookings = Booking::whereIn('status', ['pending', 'dp_lunas', 'lunas'])
            ->whereDate('start_time', '>=', Carbon::today())
            ->get(['billiard_table_id', 'start_time', 'end_time'])
            ->map(function ($b) {
                return [
                    'table_id' => $b->billiard_table_id,
                    'date'     => Carbon::parse($b->start_time)->format('Y-m-d'),
                    'start'    => Carbon::parse($b->start_time)->format('H:i'),
                    'end'      => Carbon::parse($b->end_time)->format('H:i'),
                ];
            });

        return view('admin.walkin-create', compact('tables', 'existingBookings', 'openHour', 'closeHour'));
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

    // Endpoint Real-time untuk Dashboard Admin (Tabel pesanan terbaru & metrik)
    public function realtimeDashboard()
    {
        Booking::where('status', 'dp_lunas')
            ->where('end_time', '<', Carbon::now())
            ->update(['status' => 'no_show']);

        $selesaiCount = Booking::where('status', 'lunas')->count();
        $terkonfirmasiCount = Booking::where('status', 'dp_lunas')->count();
        $bookingSuksesCount = $selesaiCount + $terkonfirmasiCount;
        $totalRevenue = Booking::whereIn('status', ['lunas', 'dp_lunas'])->sum('paid_amount');

        $pendingBookings = Booking::where('status', 'pending')->get();
        $pendingCount = $pendingBookings->count();
        $pendingDpTotal = $pendingBookings->sum('dp_amount');

        $allTables = BilliardTable::all();
        $totalTablesCount = $allTables->count();
        $activeTablesCount = $allTables->where('is_active', 1)->count();

        $recentBookings = Booking::with(['user', 'billiardTable'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'code' => '#CP-' . str_pad($b->id, 6, '0', STR_PAD_LEFT),
                    'created_at_formatted' => $b->created_at->format('d M Y, H:i'),
                    'customer' => $b->is_walkin ? $b->guest_name : ($b->user->name ?? '-'),
                    'source' => $b->is_walkin ? 'Walk-in Kasir' : 'Online Booking',
                    'table_info' => 'Meja ' . ($b->billiardTable->table_number ?? '-') . ' · ' . ($b->billiardTable->type ?? 'Reguler'),
                    'time_info' => Carbon::parse($b->start_time)->format('H.i') . ' – ' . Carbon::parse($b->end_time)->format('H.i') . ' WIB',
                    'total_formatted' => 'Rp' . number_format($b->total_price, 0, ',', '.'),
                    'paid_amount_formatted' => 'Rp' . number_format($b->paid_amount, 0, ',', '.'),
                    'dp_amount_formatted' => 'Rp' . number_format($b->dp_amount, 0, ',', '.'),
                    'status' => $b->status,
                ];
            });

        return response()->json([
            'status' => 'success',
            'metrics' => [
                'bookingSuksesCount' => $bookingSuksesCount,
                'selesaiCount' => $selesaiCount,
                'terkonfirmasiCount' => $terkonfirmasiCount,
                'totalRevenue' => (int) $totalRevenue,
                'totalRevenueFormatted' => 'Rp' . number_format($totalRevenue, 0, ',', '.'),
                'pendingCount' => $pendingCount,
                'pendingDpTotal' => (int) $pendingDpTotal,
                'pendingDpTotalFormatted' => 'Rp' . number_format($pendingDpTotal, 0, ',', '.'),
                'activeTablesCount' => $activeTablesCount,
                'totalTablesCount' => $totalTablesCount,
            ],
            'recentBookings' => $recentBookings,
            'timestamp' => Carbon::now()->toIso8601String(),
        ]);
    }

    // Endpoint Real-time untuk Kelola Pesanan Admin
    public function realtimeBookings()
    {
        Booking::where('status', 'dp_lunas')
            ->where('end_time', '<', Carbon::now())
            ->update(['status' => 'no_show']);

        $bookings = Booking::with(['user', 'billiardTable'])
            ->orderBy('created_at', 'desc')
            ->get();

        $metrics = [
            'totalOrders' => $bookings->count(),
            'suksesOrders' => $bookings->whereIn('status', ['lunas', 'dp_lunas'])->count(),
            'pendingOrders' => $bookings->where('status', 'pending')->count(),
            'noShowOrders' => $bookings->where('status', 'no_show')->count(),
            'batalOrders' => $bookings->where('status', 'cancelled')->count(),
        ];

        $orders = $bookings->map(function ($b) {
            $custName = $b->is_walkin ? $b->guest_name : ($b->user->name ?? '-');
            $custEmail = $b->is_walkin ? '-' : ($b->user->email ?? '-');
            $src = $b->is_walkin ? 'Walk-in' : 'Online';
            $code = '#CP-' . str_pad($b->id, 6, '0', STR_PAD_LEFT);
            $duration = Carbon::parse($b->start_time)->diffInHours(Carbon::parse($b->end_time)) . ' jam';
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
                'table_number' => $b->billiardTable->table_number ?? '-',
                'table_type' => $b->billiardTable->type ?? 'Reguler',
                'date' => Carbon::parse($b->start_time)->translatedFormat('d M Y'),
                'time' => Carbon::parse($b->start_time)->format('H.i') . ' – ' . Carbon::parse($b->end_time)->format('H.i') . ' WIB',
                'duration' => $duration,
                'total_price' => (int) $b->total_price,
                'total_formatted' => 'Rp' . number_format($b->total_price, 0, ',', '.'),
                'dp_amount' => (int) $b->dp_amount,
                'dp_formatted' => 'Rp' . number_format($b->dp_amount, 0, ',', '.'),
                'paid_amount' => (int) $b->paid_amount,
                'paid_formatted' => 'Rp' . number_format($b->paid_amount, 0, ',', '.'),
                'remaining_formatted' => 'Rp' . number_format($remainingAmount, 0, ',', '.'),
                'status' => $b->status,
                'confirm_url' => route('admin.bookings.confirm', $b->id),
            ];
        });

        return response()->json([
            'status' => 'success',
            'metrics' => $metrics,
            'orders' => $orders,
            'timestamp' => Carbon::now()->toIso8601String(),
        ]);
    }
}