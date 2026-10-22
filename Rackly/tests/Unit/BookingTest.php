<?php

namespace Tests\Unit;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

test('can create an online booking and casts timestamps to carbon', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $start = Carbon::parse('2026-10-25 14:00:00');
    $end = Carbon::parse('2026-10-25 16:00:00');

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => $start,
        'end_time' => $end,
        'total_price' => 160000,
        'dp_amount' => 48000,
        'status' => 'pending',
    ]);

    expect($booking->id)->not->toBeNull()
        ->and($booking->start_time)->toBeInstanceOf(Carbon::class)
        ->and($booking->end_time)->toBeInstanceOf(Carbon::class)
        ->and($booking->start_time->format('H:i'))->toBe('14:00')
        ->and($booking->end_time->format('H:i'))->toBe('16:00')
        ->and($booking->status)->toBe('pending');
});

test('booking belongs to a user and billiard table', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso']);
    $table = BilliardTable::create([
        'table_number' => 'Meja 05',
        'type' => 'VIP',
        'price_per_hour' => 120000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => now()->addHour(),
        'end_time' => now()->addHours(2),
        'total_price' => 120000,
        'dp_amount' => 60000,
        'status' => 'pending',
    ]);

    expect($booking->user->id)->toBe($user->id)
        ->and($booking->user->name)->toBe('Budi Santoso')
        ->and($booking->billiardTable->id)->toBe($table->id)
        ->and($booking->billiardTable->table_number)->toBe('Meja 05');
});

test('booking has one payment relationship', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => now()->addHour(),
        'end_time' => now()->addHours(2),
        'total_price' => 80000,
        'dp_amount' => 24000,
        'status' => 'pending',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => 'DP-' . $booking->id . '-12345',
        'gross_amount' => 24000,
        'transaction_status' => 'pending',
    ]);

    expect($booking->payment)->not->toBeNull()
        ->and($booking->payment->id)->toBe($payment->id)
        ->and($booking->payment->gross_amount)->toBe(24000);
});

test('can create a walk-in booking without online user', function () {
    $table = BilliardTable::create([
        'table_number' => 'Meja 03',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => null,
        'guest_name' => 'Tamu Walk-in Rizky',
        'is_walkin' => true,
        'billiard_table_id' => $table->id,
        'start_time' => now(),
        'end_time' => now()->addHours(2),
        'total_price' => 160000,
        'dp_amount' => 0,
        'paid_amount' => 160000,
        'status' => 'lunas',
    ]);

    expect($booking->is_walkin)->toBeTrue()
        ->and($booking->user_id)->toBeNull()
        ->and($booking->guest_name)->toBe('Tamu Walk-in Rizky')
        ->and($booking->status)->toBe('lunas')
        ->and($booking->paid_amount)->toBe(160000);
});

test('calculates correct duration and total price mathematically', function () {
    $pricePerHour = 80000;
    $start = Carbon::parse('2026-10-25 13:00:00');
    $end = Carbon::parse('2026-10-25 16:00:00');

    $durationHours = $start->diffInMinutes($end) / 60;
    $totalPrice = $durationHours * $pricePerHour;

    expect($durationHours)->toBe(3.0)
        ->and($totalPrice)->toBe(240000.0);
});

test('calculates correct DP amount and remaining balance based on percentage', function () {
    $totalPrice = 240000;
    $dpPercentage = 30; // 30%

    $dpAmount = $totalPrice * ($dpPercentage / 100);
    $remainingBalance = $totalPrice - $dpAmount;

    expect($dpAmount)->toBe(72000.0)
        ->and($remainingBalance)->toBe(168000.0);
});
