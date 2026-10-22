<?php

namespace Tests\Feature;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    Setting::updateOrCreate(['key' => 'dp_percentage'], ['value' => '30']);
});

test('user can view booking form for active table', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Flow 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('bookings.create', $table));

    $response->assertStatus(200);
    $response->assertViewHas('billiardTable', $table);
    $response->assertViewHas('dpPercentage', 30);
});

test('user cannot view booking form for inactive maintenance table', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Rusak',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => false,
    ]);

    $response = $this->actingAs($user)->get(route('bookings.create', $table));

    $response->assertRedirect('/');
    $response->assertSessionHas('error');
});

test('user can view their own booking history', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja History 01',
        'type' => 'VIP',
        'price_per_hour' => 100000,
        'is_active' => true,
    ]);

    $myBooking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(13),
        'end_time' => Carbon::tomorrow()->setHour(15),
        'total_price' => 200000,
        'dp_amount' => 60000,
        'paid_amount' => 60000,
        'status' => 'dp_lunas',
    ]);

    $otherBooking = Booking::create([
        'user_id' => $otherUser->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(16),
        'end_time' => Carbon::tomorrow()->setHour(18),
        'total_price' => 200000,
        'dp_amount' => 60000,
        'paid_amount' => 60000,
        'status' => 'dp_lunas',
    ]);

    $response = $this->actingAs($user)->get(route('booking.history'));

    $response->assertStatus(200);
    $response->assertViewHas('bookings', function ($bookings) use ($myBooking, $otherBooking) {
        return $bookings->contains($myBooking) && !$bookings->contains($otherBooking);
    });
});

test('user can view checkout page for their own pending booking', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Checkout 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(10),
        'end_time' => Carbon::tomorrow()->setHour(12),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 0,
        'status' => 'pending',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'midtrans_order_id' => 'DP-MOCK-123',
        'gross_amount' => 48000,
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
        'snap_token' => 'dummy-snap-token-xyz',
    ]);

    $response = $this->actingAs($user)->get(route('booking.checkout', $booking));

    $response->assertStatus(200);
    $response->assertViewHas('snapToken', 'dummy-snap-token-xyz');
});

test('user cannot view checkout page for another users booking', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja Checkout 02',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $otherBooking = Booking::create([
        'user_id' => $otherUser->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(10),
        'end_time' => Carbon::tomorrow()->setHour(12),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 0,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get(route('booking.checkout', $otherBooking));

    $response->assertRedirect(route('booking.history'));
    $response->assertSessionHas('error');
});

test('unauthenticated guest cannot access booking pages', function () {
    $table = BilliardTable::create([
        'table_number' => 'Meja Guest 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $this->get(route('bookings.create', $table))->assertRedirect(route('login'));
    $this->get(route('booking.history'))->assertRedirect(route('login'));
});
