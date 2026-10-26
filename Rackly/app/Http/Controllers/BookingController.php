<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BilliardTable;
use App\Models\Booking;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Setting;
use Midtrans\Config;
use Midtrans\Snap;

class BookingController extends Controller
{
    // Menampilkan riwayat pesanan (history) user
    public function history()
    {
        // Otomatis ubah pesanan DP lunas yang sudah melewati jam selesai menjadi no_show
        \App\Models\Booking::where('user_id', auth()->id())
            ->where('status', 'dp_lunas')
            ->where('end_time', '<', \Carbon\Carbon::now())
            ->update(['status' => 'no_show']);

        $bookings = \App\Models\Booking::with('billiardTable')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('booking.history', compact('bookings'));
    }

    // Menampilkan form booking untuk meja tertentu
    public function create(BilliardTable $billiardTable)
    {
        // Pastikan meja yang diakses benar-benar sedang aktif
        if (!$billiardTable->is_active) {
            return redirect('/')->with('error', 'Maaf, meja ini sedang maintenance.');
        }

        $dpType = Setting::get('dp_type', 'percentage');
        $dpPercentage = (int) Setting::get('dp_percentage', 30);
        $dpNominal = (int) Setting::get('dp_nominal', 50000);
        $openHour = Setting::get('open_hour', '10:00');
        $closeHour = Setting::get('close_hour', '23:00');

        // Ambil booking aktif yang sudah ada untuk meja ini
        $existingBookings = Booking::where('billiard_table_id', $billiardTable->id)
            ->whereIn('status', ['pending', 'dp_lunas', 'lunas'])
            ->whereDate('start_time', '>=', Carbon::today())
            ->get(['start_time', 'end_time'])
            ->map(function ($b) {
                return [
                    'date' => Carbon::parse($b->start_time)->format('Y-m-d'),
                    'start' => Carbon::parse($b->start_time)->format('H:i'),
                    'end' => Carbon::parse($b->end_time)->format('H:i'),
                ];
            });

        return view('booking.create', compact('billiardTable', 'dpType', 'dpPercentage', 'dpNominal', 'existingBookings', 'openHour', 'closeHour'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'table_id' => 'required|exists:billiard_tables,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'start_time.date_format' => 'Jam mulai harus berupa format jam (contoh: 13:00).',
            'end_time.date_format' => 'Jam selesai harus berupa format jam (contoh: 15:00).',
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $table = \App\Models\BilliardTable::findOrFail($request->table_id);

        // Gabungkan tanggal dan jam menjadi format DateTime utuh
        $start = Carbon::parse($request->booking_date . ' ' . $request->start_time);
        $end = Carbon::parse($request->booking_date . ' ' . $request->end_time);

        // 2. Cek Jadwal Bentrok (Mencegah Double Booking / Overlapping)
        $isConflict = \App\Models\Booking::where('billiard_table_id', $table->id)
            ->whereIn('status', ['pending', 'dp_lunas', 'lunas', 'paid'])
            ->where(function ($query) use ($start, $end) {
                // Logika Estafet: Izinkan booking jika jam mulai = jam selesai orang lain
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })->exists();

        if ($isConflict) {
            return back()->with('error', 'Maaf, jadwal ini sudah dipesan. Silakan pilih jam lain.')->withInput();
        }

        // 3. Hitung Durasi, Harga, dan DP sesuai setting
        $dpType = Setting::get('dp_type', 'percentage');
        $dpPercentage = (int) Setting::get('dp_percentage', 30);
        $dpNominal = (int) Setting::get('dp_nominal', 50000);
        $expiryMinutes = (int) Setting::get('dp_expiry_minutes', 15);

        $durationHours = $start->diffInMinutes($end) / 60;
        $totalPrice = $durationHours * $table->price_per_hour;

        if ($dpType === 'fixed') {
            $dpAmount = min($dpNominal, $totalPrice);
        } else {
            $dpAmount = $totalPrice * ($dpPercentage / 100);
        }

        // 4. Simpan ke database (Tabel Bookings)
        $booking = \App\Models\Booking::create([
            'user_id' => auth()->id(),
            'billiard_table_id' => $table->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_price' => $totalPrice,
            'dp_amount' => $dpAmount,
            'status' => 'pending',
        ]);

        // 5. Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        // Membuat ID Order unik (Contoh: DP-1-1700000000)
        $orderId = 'DP-' . $booking->id . '-' . time();

        // 6. Simpan data ke tabel Payments
        $payment = \App\Models\Payment::create([
            'booking_id' => $booking->id,
            'midtrans_order_id' => $orderId,
            'gross_amount' => $dpAmount,
            'transaction_status' => 'pending',
        ]);

        // 7. Siapkan parameter untuk dikirim ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $dpAmount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            // Batas waktu pembayaran
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minutes',
                'duration'   => $expiryMinutes,
            ],
        ];

        // 8. Dapatkan Snap Token dari Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Simpan snap_token ke database
        $payment->update(['snap_token' => $snapToken]);

        // 9. Arahkan ke halaman Checkout (Pastikan nama foldernya 'bookings' pakai s)
        return view('booking.checkout', compact('booking', 'payment', 'snapToken', 'dpType', 'dpPercentage', 'dpNominal'));
    }

    // Melanjutkan pembayaran dari riwayat
    public function checkout(Booking $booking)
    {
        // Pastikan booking milik user yang login dan masih pending
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            return redirect()->route('booking.history')->with('error', 'Booking tidak valid atau sudah dibayar.');
        }

        $payment = $booking->payment;
        $snapToken = $payment->snap_token;
        $dpType = Setting::get('dp_type', 'percentage');
        $dpPercentage = (int) Setting::get('dp_percentage', 30);
        $dpNominal = (int) Setting::get('dp_nominal', 50000);

        return view('booking.checkout', compact('booking', 'payment', 'snapToken', 'dpType', 'dpPercentage', 'dpNominal'));
    }

    // Membatalkan pesanan
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            return redirect()->route('booking.history')->with('error', 'Booking tidak valid atau sudah dibayar.');
        }

        $booking->update(['status' => 'cancelled']);
        if ($booking->payment) {
            $booking->payment->update(['transaction_status' => 'cancel']);
        }

        return redirect()->route('booking.history')->with('success', 'Booking berhasil dibatalkan.');
    }

    public function webhook(Request $request)
    {
        // 1. Ambil Server Key dari .env untuk gembok keamanan
        $serverKey = env('MIDTRANS_SERVER_KEY');

        // 2. Buat rumus validasi (Signature Key)
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // 3. Pastikan pesan ini benar-benar datang dari Midtrans (bukan penyusup)
        if ($hashed == $request->signature_key) {

            $payment = Payment::where('midtrans_order_id', $request->order_id)->first();

            if ($payment) {
                $transactionStatus = $request->transaction_status;

                // Jika status sukses (settlement/capture) → DP lunas
                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $payment->update(['transaction_status' => 'paid']);
                    $payment->booking->update([
                        'status'      => 'dp_lunas',
                        'paid_amount' => $payment->booking->dp_amount,
                    ]);
                }
                // Jika dibatalkan atau kadaluarsa
                else if ($transactionStatus == 'cancel' || $transactionStatus == 'expire' || $transactionStatus == 'deny') {
                    $payment->update(['transaction_status' => 'failed']);
                    $payment->booking->update(['status' => 'cancelled']);
                }
            }
        }

        return response()->json(['message' => 'Webhook handled successfully']);
    }
}
