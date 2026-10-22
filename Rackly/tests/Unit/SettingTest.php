<?php

namespace Tests\Unit;

use App\Models\Setting;

test('returns default value when setting key does not exist', function () {
    $value = Setting::get('non_existent_key', 'default_value');

    expect($value)->toBe('default_value');
});

test('returns null when setting key does not exist and no default provided', function () {
    $value = Setting::get('non_existent_key');

    expect($value)->toBeNull();
});

test('can retrieve pre-seeded dp_percentage setting value', function () {
    $value = Setting::get('dp_percentage');

    expect($value)->toBe('30');
});

test('can retrieve custom stored setting value', function () {
    Setting::create([
        'key' => 'opening_hour',
        'value' => '10:00',
        'label' => 'Jam Buka Operasional',
    ]);

    $value = Setting::get('opening_hour');

    expect($value)->toBe('10:00');
});

test('can update existing setting value', function () {
    Setting::where('key', 'dp_percentage')->update(['value' => '50']);

    expect(Setting::get('dp_percentage'))->toBe('50');
});
