<?php

namespace Tests\Unit;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\User;

test('can create a billiard table with attributes', function () {
    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    expect($table->id)->not->toBeNull()
        ->and($table->table_number)->toBe('Meja 01')
        ->and($table->type)->toBe('Reguler')
        ->and($table->price_per_hour)->toBe(80000)
        ->and($table->is_active)->toBeTrue();
});

test('billiard table has bookings relationship', function () {
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

    expect($table->bookings)->toHaveCount(1)
        ->and($table->bookings->first()->id)->toBe($booking->id);
});

test('can mark a billiard table as maintenance / inactive', function () {
    $table = BilliardTable::create([
        'table_number' => 'Meja 03',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $table->update(['is_active' => false]);

    expect($table->fresh()->is_active)->toBeFalse();
});
