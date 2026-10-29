<?php

namespace Tests\Feature;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

test('midtrans webhook settlement is idempotent on duplicate delivery', function () {
    config(['midtrans.server_key' => 'test-server-key']);
    putenv('MIDTRANS_SERVER_KEY=test-server-key');

    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Idempotent 01',
        'type' => 'Reguler',
        'price_per_hour' => 100000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(14),
        'end_time' => Carbon::tomorrow()->setHour(16),
        'total_price' => 200000,
        'dp_amount' => 60000,
        'paid_amount' => 0,
        'status' => 'pending',
    ]);

    $orderId = 'MID-IDEMP-' . uniqid();
    Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => $orderId,
        'gross_amount' => 60000,
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
    ]);

    $payload = [
        'order_id' => $orderId,
        'status_code' => '200',
        'gross_amount' => '60000.00',
        'signature_key' => hash('sha512', $orderId . '200' . '60000.00' . 'test-server-key'),
        'transaction_status' => 'settlement',
    ];

    // First webhook delivery
    $response1 = $this->postJson(route('midtrans.webhook'), $payload);
    $response1->assertStatus(200);

    $booking->refresh();
    expect($booking->status)->toBe('dp_lunas')
        ->and((int) $booking->paid_amount)->toBe(60000);

    // Second webhook delivery (duplicate / retry by Midtrans)
    $response2 = $this->postJson(route('midtrans.webhook'), $payload);
    $response2->assertStatus(200);

    $booking->refresh();
    expect($booking->status)->toBe('dp_lunas')
        ->and((int) $booking->paid_amount)->toBe(60000);
});

test('midtrans settlement webhook does not downgrade an already lunas booking', function () {
    config(['midtrans.server_key' => 'test-server-key']);
    putenv('MIDTRANS_SERVER_KEY=test-server-key');

    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Idempotent 02',
        'type' => 'Reguler',
        'price_per_hour' => 100000,
        'is_active' => true,
    ]);

    // Booking already confirmed full payment (lunas)
    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(14),
        'end_time' => Carbon::tomorrow()->setHour(16),
        'total_price' => 200000,
        'dp_amount' => 60000,
        'paid_amount' => 200000,
        'status' => 'lunas',
    ]);

    $orderId = 'MID-LUNAS-' . uniqid();
    Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => $orderId,
        'gross_amount' => 60000,
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
    ]);

    $payload = [
        'order_id' => $orderId,
        'status_code' => '200',
        'gross_amount' => '60000.00',
        'signature_key' => hash('sha512', $orderId . '200' . '60000.00' . 'test-server-key'),
        'transaction_status' => 'settlement',
    ];

    $response = $this->postJson(route('midtrans.webhook'), $payload);
    $response->assertStatus(200);

    $booking->refresh();
    // Must remain lunas with full paid_amount, NOT reverted to dp_lunas
    expect($booking->status)->toBe('lunas')
        ->and((int) $booking->paid_amount)->toBe(200000);
});

test('midtrans expire webhook does not cancel an already paid booking', function () {
    config(['midtrans.server_key' => 'test-server-key']);
    putenv('MIDTRANS_SERVER_KEY=test-server-key');

    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Idempotent 03',
        'type' => 'Reguler',
        'price_per_hour' => 100000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(14),
        'end_time' => Carbon::tomorrow()->setHour(16),
        'total_price' => 200000,
        'dp_amount' => 60000,
        'paid_amount' => 60000,
        'status' => 'dp_lunas',
    ]);

    $orderId = 'MID-EXPIRE-' . uniqid();
    Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => $orderId,
        'gross_amount' => 60000,
        'payment_type' => 'qris',
        'transaction_status' => 'paid',
    ]);

    $payload = [
        'order_id' => $orderId,
        'status_code' => '200',
        'gross_amount' => '60000.00',
        'signature_key' => hash('sha512', $orderId . '200' . '60000.00' . 'test-server-key'),
        'transaction_status' => 'expire',
    ];

    $response = $this->postJson(route('midtrans.webhook'), $payload);
    $response->assertStatus(200);

    $booking->refresh();
    // Must remain dp_lunas, not cancelled
    expect($booking->status)->toBe('dp_lunas');
});

test('admin confirmFullPayment is idempotent on repeated clicks', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja Pelunasan Idempotent',
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

    // First confirmation
    $response1 = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking));
    $response1->assertRedirect(route('admin.bookings'));
    $response1->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('lunas');

    // Second confirmation (double click / repeated request)
    $response2 = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking));
    $response2->assertRedirect(route('admin.bookings'));
    $response2->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('lunas');
});

test('user cancel booking is idempotent on repeated clicks', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Batal Idempotent',
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

    // First cancel request
    $response1 = $this->actingAs($user)->post(route('booking.cancel', $booking->id));
    $response1->assertRedirect(route('booking.history'));
    $response1->assertSessionHas('success');

    expect($booking->fresh()->status)->toBe('cancelled');

    // Second cancel request (double click / retry)
    $response2 = $this->actingAs($user)->post(route('booking.cancel', $booking->id));
    $response2->assertRedirect(route('booking.history'));
    $response2->assertSessionHas('success');

    expect($booking->fresh()->status)->toBe('cancelled');
});

test('admin storeWalkIn is idempotent on repeated form submission', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $table = BilliardTable::create([
        'table_number' => 'Meja WalkIn Idempotent',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');
    $payload = [
        'guest_name' => 'Budi Idempotent',
        'billiard_table_id' => $table->id,
        'booking_date' => $tomorrow,
        'start_time' => '13:00',
        'end_time' => '15:00',
    ];

    // First submission
    $response1 = $this->actingAs($admin)->post(route('admin.walkin.store'), $payload);
    $response1->assertRedirect(route('admin.bookings'));
    $response1->assertSessionHas('success');

    $bookingCountAfterFirst = Booking::where('guest_name', 'Budi Idempotent')->count();
    expect($bookingCountAfterFirst)->toBe(1);

    // Second submission (double click by cashier)
    $response2 = $this->actingAs($admin)->post(route('admin.walkin.store'), $payload);
    $response2->assertRedirect(route('admin.bookings'));
    $response2->assertSessionHas('success');

    // No duplicate booking row created
    $bookingCountAfterSecond = Booking::where('guest_name', 'Budi Idempotent')->count();
    expect($bookingCountAfterSecond)->toBe(1);
});

