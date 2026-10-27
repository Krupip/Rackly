<?php

namespace App\Http\Controllers;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Hanya tampilkan meja yang statusnya aktif/tersedia
        $tables = BilliardTable::where('is_active', 1)->get();
        $dpType       = Setting::get('dp_type', 'percentage');
        $dpPercentage = (int) Setting::get('dp_percentage', 30);
        $dpNominal    = (int) Setting::get('dp_nominal', 50000);
        $openHour     = Setting::get('open_hour', '10:00');
        $closeHour    = Setting::get('close_hour', '23:00');

        $existingBookings = Booking::whereIn('status', ['pending', 'dp_lunas', 'lunas'])
            ->whereDate('start_time', '>=', Carbon::today())
            ->get(['billiard_table_id', 'start_time', 'end_time'])
            ->map(function ($b) {
                return [
                    'table_id' => (int) $b->billiard_table_id,
                    'date'     => Carbon::parse($b->start_time)->format('Y-m-d'),
                    'start'    => Carbon::parse($b->start_time)->format('H:i'),
                    'end'      => Carbon::parse($b->end_time)->format('H:i'),
                ];
            });

        return view('home', compact('tables', 'dpType', 'dpPercentage', 'dpNominal', 'openHour', 'closeHour', 'existingBookings'));
    }

    // Endpoint Real-time untuk katalog meja di halaman Beranda
    public function realtimeTables()
    {
        $now = Carbon::now();
        $tables = BilliardTable::where('is_active', 1)->get()->map(function ($table, $index) use ($now) {
            // Cek apakah meja sedang aktif dipakai bermain pada jam ini
            $isOccupied = Booking::where('billiard_table_id', $table->id)
                ->whereIn('status', ['dp_lunas', 'lunas'])
                ->where('start_time', '<=', $now)
                ->where('end_time', '>', $now)
                ->exists();

            return [
                'id'              => $table->id,
                'table_number'    => $table->table_number,
                'type'            => $table->type,
                'index_label'     => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'price_per_hour'  => (int) $table->price_per_hour,
                'formatted_price' => 'Rp ' . number_format($table->price_per_hour, 0, ',', '.'),
                'is_occupied'     => $isOccupied,
                'booking_url'     => route('bookings.create', $table->id),
            ];
        });

        $existingBookings = Booking::whereIn('status', ['pending', 'dp_lunas', 'lunas'])
            ->whereDate('start_time', '>=', Carbon::today())
            ->get(['billiard_table_id', 'start_time', 'end_time'])
            ->map(function ($b) {
                return [
                    'table_id' => (int) $b->billiard_table_id,
                    'date'     => Carbon::parse($b->start_time)->format('Y-m-d'),
                    'start'    => Carbon::parse($b->start_time)->format('H:i'),
                    'end'      => Carbon::parse($b->end_time)->format('H:i'),
                ];
            });

        return response()->json([
            'status'           => 'success',
            'tables'           => $tables,
            'existingBookings' => $existingBookings,
            'count'            => $tables->count(),
            'timestamp'        => $now->toIso8601String(),
        ]);
    }
}
