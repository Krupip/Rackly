<?php

use App\Models\Setting;

it('returns a successful response on home page with percentage dp', function () {
    Setting::set('dp_type', 'percentage');
    Setting::set('dp_percentage', 30);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('30%');
    $response->assertSee('Amankan dengan DP 30%');
});

it('displays fixed dp nominal on home page when dp_type is fixed', function () {
    Setting::set('dp_type', 'fixed');
    Setting::set('dp_nominal', 50000);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Rp50.000');
    $response->assertSee('Amankan dengan DP Rp50.000');
});
