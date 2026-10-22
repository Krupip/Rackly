<?php

namespace Tests\Unit;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\User;

test('new user has user role by default', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe('user');
});

test('can create admin user', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    expect($admin->role)->toBe('admin');
});

test('password and remember token are hidden from serialization', function () {
    $user = User::factory()->create([
        'password' => 'secret123',
    ]);

    $array = $user->toArray();

    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('remember_token');
});

test('user has many bookings', function () {
    $user = User::factory()->create();
    $table = BilliardTable::create([
        'table_number' => 'Meja 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => now()->addHour(),
        'end_time' => now()->addHours(2),
        'total_price' => 80000,
        'dp_amount' => 24000,
        'status' => 'pending',
    ]);

    Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => now()->addHours(3),
        'end_time' => now()->addHours(5),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'status' => 'pending',
    ]);

    expect($user->bookings)->toHaveCount(2);
});
