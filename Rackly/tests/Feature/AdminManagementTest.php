<?php

namespace Tests\Feature;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;

test('admin can view admin dashboard with correct metrics', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);

    $table = BilliardTable::create([
        'table_number' => 'Meja Stat 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    // Create completed booking
    Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::yesterday()->setHour(10),
        'end_time' => Carbon::yesterday()->setHour(12),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 160000,
        'status' => 'lunas',
    ]);

    // Create dp_lunas booking
    Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(14),
        'end_time' => Carbon::tomorrow()->setHour(16),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 48000,
        'status' => 'dp_lunas',
    ]);

    // Create pending booking
    Booking::create([
        'user_id' => $user->id,
        'billiard_table_id' => $table->id,
        'start_time' => Carbon::tomorrow()->setHour(18),
        'end_time' => Carbon::tomorrow()->setHour(20),
        'total_price' => 160000,
        'dp_amount' => 48000,
        'paid_amount' => 0,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('bookingSuksesCount', 2);
    $response->assertViewHas('totalRevenue', 208000); // 160000 + 48000
    $response->assertViewHas('pendingCount', 1);
});

test('regular user cannot access admin dashboard', function () {
    $regularUser = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($regularUser)->get(route('admin.dashboard'));

    $response->assertRedirect('/');
    $response->assertSessionHas('error');
});

test('admin can create a new billiard table', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/admin/tables', [
        'table_number' => 'Meja Baru 99',
        'type' => 'VVIP',
        'price_per_hour' => 120000,
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('tables.index'));

    $this->assertDatabaseHas('billiard_tables', [
        'table_number' => 'Meja Baru 99',
        'type' => 'VVIP',
        'price_per_hour' => 120000,
        'is_active' => 1,
    ]);
});

test('admin can update billiard table details', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $table = BilliardTable::create([
        'table_number' => 'Meja Edit 01',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->put("/admin/tables/{$table->id}", [
        'table_number' => 'Meja Edit 01 Updated',
        'type' => 'VIP',
        'price_per_hour' => 100000,
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('tables.index'));

    $table->refresh();
    expect($table->table_number)->toBe('Meja Edit 01 Updated')
        ->and($table->type)->toBe('VIP')
        ->and($table->price_per_hour)->toBe(100000)
        ->and($table->is_active)->toBeFalse();
});

test('admin can delete a billiard table', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $table = BilliardTable::create([
        'table_number' => 'Meja Hapus',
        'type' => 'Reguler',
        'price_per_hour' => 80000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->delete("/admin/tables/{$table->id}");

    $response->assertRedirect(route('tables.index'));

    $this->assertDatabaseMissing('billiard_tables', [
        'id' => $table->id,
    ]);
});

test('admin can view and update system settings', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.settings'));
    $response->assertStatus(200);

    $updateResponse = $this->actingAs($admin)->patch(route('admin.settings.update'), [
        'dp_percentage' => 45,
    ]);

    $updateResponse->assertRedirect(route('admin.settings'));
    $updateResponse->assertSessionHas('success');

    expect(Setting::get('dp_percentage'))->toBe('45');
});

test('admin settings validation fails with invalid percentage', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->patch(route('admin.settings.update'), [
        'dp_percentage' => 150, // exceeds max:100
    ]);

    $response->assertSessionHasErrors(['dp_percentage']);
});

test('admin can update DP setting to fixed nominal', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->patch(route('admin.settings.update'), [
        'dp_type' => 'fixed',
        'dp_nominal' => 75000,
        'dp_expiry_minutes' => 30,
    ]);

    $response->assertRedirect(route('admin.settings'));
    $response->assertSessionHas('success');

    expect(Setting::get('dp_type'))->toBe('fixed')
        ->and(Setting::get('dp_nominal'))->toBe('75000')
        ->and(Setting::get('dp_expiry_minutes'))->toBe('30');
});

test('admin settings validation fails when fixed nominal is missing or invalid', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->patch(route('admin.settings.update'), [
        'dp_type' => 'fixed',
        'dp_nominal' => 500, // below min:1000
    ]);

    $response->assertSessionHasErrors(['dp_nominal']);
});

