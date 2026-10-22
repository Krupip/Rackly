<?php

namespace Tests\Unit;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;

test('can create a payment record with midtrans order id', function () {
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
        'midtrans_order_id' => 'DP-' . $booking->id . '-1700000000',
        'gross_amount' => 24000,
        'transaction_status' => 'pending',
        'snap_token' => 'sample-snap-token-abc-123',
    ]);

    expect($payment->id)->not->toBeNull()
        ->and($payment->midtrans_order_id)->toBe('DP-' . $booking->id . '-1700000000')
        ->and($payment->gross_amount)->toBe(24000)
        ->and($payment->snap_token)->toBe('sample-snap-token-abc-123')
        ->and($payment->transaction_status)->toBe('pending');
});

test('payment belongs to booking', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja 02',
        'type' => 'VIP',
        'price_per_hour' => 120000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => now()->addHour(),
        'end_time' => now()->addHours(3),
        'total_price' => 240000,
        'dp_amount' => 72000,
        'status' => 'pending',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => 'DP-' . $booking->id . '-999',
        'gross_amount' => 72000,
        'transaction_status' => 'pending',
    ]);

    expect($payment->booking->id)->toBe($booking->id)
        ->and($payment->booking->total_price)->toBe(240000);
});

test('can update payment transaction status when midtrans webhook confirms', function () {
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
        'midtrans_order_id' => 'DP-' . $booking->id . '-test',
        'gross_amount' => 24000,
        'transaction_status' => 'pending',
    ]);

    $payment->update(['transaction_status' => 'paid']);

    expect($payment->fresh()->transaction_status)->toBe('paid');
});
