<?php

namespace Tests\Feature;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

test('admin can access walk-in creation page and view existing bookings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja T1',
        'type' => 'Reguler',
        'price_per_hour' => 50000,
        'is_active' => true,
    ]);

    Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setTime(14, 0),
        'end_time' => Carbon::tomorrow()->setTime(16, 0),
        'total_price' => 100000,
        'dp_amount' => 30000,
        'paid_amount' => 30000,
        'status' => 'dp_lunas',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.walkin.create'));

    $response->assertStatus(200);
    $response->assertViewHas('tables');
    $response->assertViewHas('existingBookings');

    $bookingsInView = $response->viewData('existingBookings');
    expect($bookingsInView)->not->toBeEmpty();
    expect($bookingsInView->first()['table_id'])->toBe($table->id);
    expect($bookingsInView->first()['start'])->toBe('14:00');
    expect($bookingsInView->first()['end'])->toBe('16:00');
});

test('admin can create a walk-in booking without paying dp', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $table = BilliardTable::create([
        'table_number' => 'Meja Walk-In 01',
        'type' => 'VIP',
        'price_per_hour' => 100000,
        'is_active' => true,
    ]);

    $date = Carbon::tomorrow()->format('Y-m-d');

    $response = $this->actingAs($admin)->post(route('admin.walkin.store'), [
        'guest_name' => 'John Walkin',
        'billiard_table_id' => $table->id,
        'booking_date' => $date,
        'start_time' => '13:00',
        'end_time' => '15:00',
    ]);

    $response->assertRedirect(route('admin.bookings'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('bookings', [
        'billiard_table_id' => $table->id,
        'guest_name' => 'John Walkin',
        'is_walkin' => 1,
        'user_id' => null,
        'total_price' => 200000,
        'dp_amount' => 0,
        'paid_amount' => 200000,
        'status' => 'lunas',
    ]);
});

test('walk-in booking fails when timeslot conflicts with existing booking', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $table = BilliardTable::create([
        'table_number' => 'Meja Walk-In 02',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $date = Carbon::tomorrow()->format('Y-m-d');
    $start = Carbon::parse("{$date} 14:00");
    $end = Carbon::parse("{$date} 16:00");

    // Existing confirmed booking from an online user
    Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => $start,
        'end_time' => $end,
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 48000,
        'status' => 'dp_lunas',
    ]);

    // Admin tries to create walk-in overlapping (15:00 - 17:00)
    $response = $this->actingAs($admin)->from(route('admin.walkin.create'))->post(route('admin.walkin.store'), [
        'guest_name' => 'Conflicted Guest',
        'billiard_table_id' => $table->id,
        'booking_date' => $date,
        'start_time' => '15:00',
        'end_time' => '17:00',
    ]);

    $response->assertRedirect(route('admin.walkin.create'));
    $response->assertSessionHas('error');

    $this->assertDatabaseMissing('bookings', [
        'guest_name' => 'Conflicted Guest',
    ]);
});

test('walk-in booking validation fails when end_time is not after start_time', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $table = BilliardTable::create([
        'table_number' => 'Meja Walk-In 03',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $date = Carbon::tomorrow()->format('Y-m-d');

    $response = $this->actingAs($admin)->post(route('admin.walkin.store'), [
        'guest_name' => 'Invalid Times Guest',
        'billiard_table_id' => $table->id,
        'booking_date' => $date,
        'start_time' => '15:00',
        'end_time' => '14:00',
    ]);

    $response->assertSessionHasErrors(['end_time']);
});

test('unauthenticated user cannot create walk-in booking', function () {
    $response = $this->post(route('admin.walkin.store'), [
        'guest_name' => 'Hacker Guest',
    ]);

    $response->assertRedirect(route('login'));
});
