<?php

use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;
use Eclipse\World\Models\CountryInSpecialRegion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can assign geo region to country', function () {
    $region = Region::factory()->create(['name' => 'Europe']);
    $country = Country::factory()->create(['region_id' => $region->id]);

    expect($country->region)->not->toBeNull()
        ->and($country->region->name)->toBe('Europe');
});

it('can assign special regions to country', function () {
    $region = Region::factory()->create(['name' => 'European Union', 'is_special' => true]);
    $country = Country::factory()->create();

    $country->specialRegions()->attach($region->id, [
        'start_date' => now()->subYear(),
    ]);

    expect($country->specialRegions)->toHaveCount(1)
        ->and($country->specialRegions->first()->name)->toBe('European Union');
});

it('can check if country is currently in special region', function () {
    $region = Region::factory()->create(['name' => 'European Union', 'code' => 'EU', 'is_special' => true]);
    $country = Country::factory()->create();

    $country->specialRegions()->attach($region->id, [
        'start_date' => now()->subYears(2),
        'end_date' => now()->addYears(2),
    ]);

    expect($country->inSpecialRegion('EU'))->toBeTrue();
});

it('can detect if country is not in special region when date is expired', function () {
    $region = Region::factory()->create(['name' => 'European Union', 'code' => 'EU', 'is_special' => true]);
    $country = Country::factory()->create();

    $country->specialRegions()->attach($region->id, [
        'start_date' => now()->subYears(5),
        'end_date' => now()->subYears(1),
    ]);

    expect($country->inSpecialRegion('EU'))->toBeFalse();
});
