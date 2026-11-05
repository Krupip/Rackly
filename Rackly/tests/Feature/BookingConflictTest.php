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

test('prevents booking overlapping time slots on the same table', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');

    // Existing booking: 14:00 - 17:00
    Booking::create([
        'user_id' => $user1->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::parse("$tomorrow 14:00:00"),
        'end_time' => Carbon::parse("$tomorrow 17:00:00"),
        'total_price' => 240000,
        'dp_amount' => 72000,
        'status' => 'pending',
    ]);

    // Try booking 15:00 - 18:00 (overlaps with 14:00-17:00)
    $response = $this->actingAs($user2)->post(route('bookings.store'), [
        'table_id' => $table->id,
        'booking_date' => $tomorrow,
        'start_time' => '15:00',
        'end_time' => '18:00',
    ]);

    $response->assertSessionHas('error');
    expect(Booking::where('user_id', $user2->id)->count())->toBe(0);
});

test('allows back-to-back estafet bookings when end time equals start time', function () {
    $user1 = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');

    // Existing booking: 14:00 - 16:00
    Booking::create([
        'user_id' => $user1->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::parse("$tomorrow 14:00:00"),
        'end_time' => Carbon::parse("$tomorrow 16:00:00"),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'status' => 'pending',
    ]);

    // New booking starting exactly at 16:00 (estafet)
    // Check manual conflict check logic
    $start = Carbon::parse("$tomorrow 16:00:00");
    $end = Carbon::parse("$tomorrow 18:00:00");

    $isConflict = Booking::where('billiard_table_id', $table->id)
        ->whereIn('status', ['pending', 'dp_lunas', 'lunas', 'paid'])
        ->where(function ($query) use ($start, $end) {
            $query->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
        })->exists();

    expect($isConflict)->toBeFalse();
});

test('allows booking at the same time on different tables', function () {
    $user1 = User::factory()->create();

    $table1 = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $table2 = BilliardTable::create([
        'table_number' => 'Meja 02',
        'type' => 'VIP',
        'price_per_hour' => 120000,
        'is_active' => true,
    ]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');

    // Booking on Table 1: 14:00 - 16:00
    Booking::create([
        'user_id' => $user1->id,
        'billiard_table_id' => $table1->id,
        'start_time' => Carbon::parse("$tomorrow 14:00:00"),
        'end_time' => Carbon::parse("$tomorrow 16:00:00"),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'status' => 'pending',
    ]);

    // Check conflict on Table 2 for same time: 14:00 - 16:00
    $start = Carbon::parse("$tomorrow 14:00:00");
    $end = Carbon::parse("$tomorrow 16:00:00");

    $isConflict = Booking::where('billiard_table_id', $table2->id)
        ->whereIn('status', ['pending', 'dp_lunas', 'lunas', 'paid'])
        ->where(function ($query) use ($start, $end) {
            $query->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
        })->exists();

    expect($isConflict)->toBeFalse();
});

test('ignores cancelled bookings during conflict detection', function () {
    $user1 = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');

    // Cancelled booking: 14:00 - 16:00
    Booking::create([
        'user_id' => $user1->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::parse("$tomorrow 14:00:00"),
        'end_time' => Carbon::parse("$tomorrow 16:00:00"),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'status' => 'cancelled',
    ]);

    $start = Carbon::parse("$tomorrow 14:00:00");
    $end = Carbon::parse("$tomorrow 16:00:00");

    $isConflict = Booking::where('billiard_table_id', $table->id)
        ->whereIn('status', ['pending', 'dp_lunas', 'lunas', 'paid'])
        ->where(function ($query) use ($start, $end) {
            $query->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
        })->exists();

    expect($isConflict)->toBeFalse();
});

test('user can cancel their own pending booking', function () {
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

    $response = $this->actingAs($user)->post(route('booking.cancel', $booking->id));

    $response->assertRedirect(route('booking.history'));
    expect($booking->fresh()->status)->toBe('cancelled');
});

test('user cannot cancel someone elses booking', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $owner->id,
        'billiard_table_id' => $table->id,
        'start_time' => now()->addHour(),
        'end_time' => now()->addHours(2),
        'total_price' => 80000,
        'dp_amount' => 24000,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($otherUser)->post(route('booking.cancel', $booking->id));

    $response->assertSessionHas('error');
    expect($booking->fresh()->status)->toBe('pending');
});

test('user cannot book a date in the past', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Past Date',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $yesterday = Carbon::yesterday()->format('Y-m-d');

    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'table_id' => $table->id,
        'booking_date' => $yesterday,
        'start_time' => '14:00',
        'end_time' => '16:00',
    ]);

    $response->assertSessionHasErrors('booking_date');
    expect(Booking::where('user_id', $user->id)->count())->toBe(0);
});

test('user cannot book a past hour for today', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Past Hour',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    // Freeze time to 15:00 today
    Carbon::setTestNow(Carbon::today()->setHour(15));

    $today = Carbon::today()->format('Y-m-d');

    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'table_id' => $table->id,
        'booking_date' => $today,
        'start_time' => '11:00',
        'end_time' => '13:00',
    ]);

    $response->assertSessionHas('error');
    expect(Booking::where('user_id', $user->id)->count())->toBe(0);

    Carbon::setTestNow(); // reset
});

test('user cannot create a new booking if they already have an active pending booking', function () {
    $user = User::factory()->create();
    $table1 = BilliardTable::create([
        'table_number' => 'Meja Inventory 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);
    $table2 = BilliardTable::create([
        'table_number' => 'Meja Inventory 02',
        'type' => 'VIP',
        'price_per_hour' => 120000,
        'is_active' => true,
    ]);

    $tomorrow = Carbon::tomorrow()->format('Y-m-d');

    // Booking 1: already created and still pending
    $pendingBooking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table1->id,
        'start_time' => Carbon::parse("$tomorrow 14:00:00"),
        'end_time' => Carbon::parse("$tomorrow 16:00:00"),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'status' => 'pending',
    ]);

    Payment::create([
        'booking_id' => $pendingBooking->id,
        'midtrans_order_id' => 'DP-PENDING-TEST-1',
        'gross_amount' => 48000,
        'transaction_status' => 'pending',
        'snap_token' => 'mock-token-1',
    ]);

    // User attempts to create Booking 2 on Table 2 while Booking 1 is still pending
    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'table_id' => $table2->id,
        'booking_date' => $tomorrow,
        'start_time' => '17:00',
        'end_time' => '19:00',
    ]);

    // Should be redirected to checkout of existing pending booking with error flash
    $response->assertRedirect(route('booking.checkout', $pendingBooking));
    $response->assertSessionHas('error');

    // Total bookings for user must remain 1
    expect(Booking::where('user_id', $user->id)->count())->toBe(1);
});

test('user is redirected to active pending checkout when accessing create form', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Form Block 01',
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
        'status' => 'pending',
    ]);

    Payment::create([
        'booking_id' => $pendingBooking->id,
        'midtrans_order_id' => 'DP-FORM-TEST-1',
        'gross_amount' => 48000,
        'transaction_status' => 'pending',
        'snap_token' => 'mock-token-form',
    ]);

    $response = $this->actingAs($user)->get(route('bookings.create', $table));

    $response->assertRedirect(route('booking.checkout', $pendingBooking));
    $response->assertSessionHas('error');
});

test('expired pending booking is auto-cancelled and does not block new booking', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja Expired Test',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    // Create an old pending booking created 30 minutes ago (exceeds default 15 min expiry)
    $expiredBooking = Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(14),
        'end_time' => Carbon::tomorrow()->setHour(16),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'status' => 'pending',
    ]);
    \Illuminate\Support\Facades\DB::table('bookings')
        ->where('id', $expiredBooking->id)
        ->update(['created_at' => Carbon::now()->subMinutes(30)]);

    // User can access create form because expired booking is cleared
    $response = $this->actingAs($user)->get(route('bookings.create', $table));
    $response->assertStatus(200);

    // The expired booking must now have status 'cancelled'
    expect($expiredBooking->fresh()->status)->toBe('cancelled');
});


