<?php

use Eclipse\World\Filament\Clusters\World\Resources\CountryResource;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;

beforeEach(function () {
    $this->setUpSuperAdmin();
});

test('can browse country resource page', function () {
    $region = Region::factory()->create(['name' => 'Test Region']);
    $country = Country::factory()->create([
        'name' => 'Test Country',
        'id' => 'TC',
        'region_id' => $region->id,
    ]);

    $this->visit(CountryResource::getUrl())
        ->assertSee('Countries')
        ->assertSee('Test Country')
        ->assertSee('TC')
        ->assertSee('Test Region');
});

test('can interact with country resource table', function () {
    $region = Region::factory()->create(['name' => 'Browser Test Region']);
    $country = Country::factory()->create([
        'name' => 'Browser Test Country',
        'id' => 'BTC',
        'region_id' => $region->id,
    ]);

    $this->visit(CountryResource::getUrl())
        ->assertSee('Browser Test Country')
        ->assertSee('BTC')
        ->assertSee('Browser Test Region')
        ->assertSee('Countries');
});

test('country resource page loads without JavaScript errors', function () {
    $this->visit(CountryResource::getUrl())
        ->assertSee('Countries')
        ->assertDontSee('Uncaught')
        ->assertDontSee('ReferenceError')
        ->assertDontSee('TypeError')
        ->assertDontSee('SyntaxError');
});
