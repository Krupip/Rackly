<?php

namespace Tests\Feature;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

test('admin can view bookings management list', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.bookings'));

    $response->assertStatus(200);
});

test('admin can confirm full payment for dp_lunas booking', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja Pelunasan 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(14),
        'end_time' => Carbon::tomorrow()->setHour(16),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 48000,
        'status' => 'dp_lunas',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking));

    $response->assertRedirect(route('admin.bookings'));
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('lunas')
        ->and((int) $booking->paid_amount)->toBe(160000);
});

test('admin cannot confirm payment for booking that is not dp_lunas', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja Pelunasan 02',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $pendingBooking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(10),
        'end_time' => Carbon::tomorrow()->setHour(12),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 0,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bookings.confirm', $pendingBooking));

    $response->assertRedirect(route('admin.bookings'));
    $response->assertSessionHas('error');

    $pendingBooking->refresh();
    expect($pendingBooking->status)->toBe('pending');
});

test('midtrans webhook updates booking to dp_lunas on settlement', function () {
    config(['midtrans.server_key' => 'test-server-key']);
    putenv('MIDTRANS_SERVER_KEY=test-server-key');

    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Midtrans 01',
        'type' => 'Reguler',
        'price_per_hour' => 100000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(18),
        'end_time' => Carbon::tomorrow()->setHour(20),
        'total_price' => 200000,
        'dp_amount' => 60000,
        'paid_amount' => 0,
        'status' => 'pending',
    ]);

    $orderId = 'MID-TEST-' . uniqid();
    Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => $orderId,
        'gross_amount' => 60000,
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
    ]);

    $statusCode = '200';
    $grossAmount = '60000.00';
    $serverKey = 'test-server-key';
    $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

    $response = $this->postJson(route('midtrans.webhook'), [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signature,
        'transaction_status' => 'settlement',
    ]);

    $response->assertStatus(200);

    $booking->refresh();
    expect($booking->status)->toBe('dp_lunas')
        ->and((int) $booking->paid_amount)->toBe(60000);
});

test('midtrans webhook cancels booking on expire or cancel', function () {
    config(['midtrans.server_key' => 'test-server-key']);
    putenv('MIDTRANS_SERVER_KEY=test-server-key');

    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Midtrans 02',
        'type' => 'Reguler',
        'price_per_hour' => 100000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(18),
        'end_time' => Carbon::tomorrow()->setHour(20),
        'total_price' => 200000,
        'dp_amount' => 60000,
        'paid_amount' => 0,
        'status' => 'pending',
    ]);

    $orderId = 'MID-CANCEL-' . uniqid();
    Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => $orderId,
        'gross_amount' => 60000,
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
    ]);

    $statusCode = '200';
    $grossAmount = '60000.00';
    $serverKey = 'test-server-key';
    $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

    $response = $this->postJson(route('midtrans.webhook'), [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signature,
        'transaction_status' => 'expire',
    ]);

    $response->assertStatus(200);

    $booking->refresh();
    expect($booking->status)->toBe('cancelled');
});
