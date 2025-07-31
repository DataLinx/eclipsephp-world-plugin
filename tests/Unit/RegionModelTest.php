<?php

use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;
use Illuminate\Support\Carbon;

test('region can have parent and children', function () {
    $parent = Region::factory()->create();
    $child = Region::factory()->withParent($parent)->create();

    expect($child->parent)->toBeInstanceOf(Region::class);
    expect($child->parent->id)->toEqual($parent->id);
    expect($parent->children)->toHaveCount(1);
    expect($parent->children->first()->id)->toEqual($child->id);
});

test('region can have countries', function () {
    $region = Region::factory()->create();
    $country = Country::factory()->create(['region_id' => $region->id]);

    expect($region->countries)->toHaveCount(1);
    expect($region->countries->first()->id)->toEqual($country->id);
});

test('special region can have countries with dates', function () {
    $region = Region::factory()->special()->create();
    $country = Country::factory()->create();

    $startDate = Carbon::now()->subDays(30);
    $endDate = Carbon::now()->addDays(30);

    $region->specialCountries()->attach($country->id, [
        'start_date' => $startDate->toDateString(),
        'end_date' => $endDate->toDateString(),
    ]);

    expect($region->specialCountries)->toHaveCount(1);
    expect($region->specialCountries->first()->id)->toEqual($country->id);
});

test('getCountriesInSpecialRegion returns countries within date range', function () {
    $region = Region::factory()->special()->create();
    $country1 = Country::factory()->create();
    $country2 = Country::factory()->create();
    $country3 = Country::factory()->create();

    $now = Carbon::now();

    // Country 1: Active (started in past, no end date)
    $region->specialCountries()->attach($country1->id, [
        'start_date' => $now->copy()->subDays(30)->toDateString(),
        'end_date' => null,
    ]);

    // Country 2: Active (started in past, ends in future)
    $region->specialCountries()->attach($country2->id, [
        'start_date' => $now->copy()->subDays(30)->toDateString(),
        'end_date' => $now->copy()->addDays(30)->toDateString(),
    ]);

    // Country 3: Inactive (ended in past)
    $region->specialCountries()->attach($country3->id, [
        'start_date' => $now->copy()->subDays(60)->toDateString(),
        'end_date' => $now->copy()->subDays(10)->toDateString(),
    ]);

    $activeCountries = $region->getCountriesInSpecialRegion($now);

    expect($activeCountries)->toHaveCount(2);
    expect($activeCountries->pluck('id')->toArray())->toContain($country1->id, $country2->id);
    expect($activeCountries->pluck('id')->toArray())->not->toContain($country3->id);
});

test('getCountriesInSpecialRegion returns empty collection for geographical region', function () {
    $region = Region::factory()->geographical()->create();
    $country = Country::factory()->create();

    $region->specialCountries()->attach($country->id, [
        'start_date' => Carbon::now()->toDateString(),
    ]);

    $countries = $region->getCountriesInSpecialRegion();

    expect($countries)->toHaveCount(0);
});

test('isGeographical returns correct value', function () {
    $geographical = Region::factory()->geographical()->create();
    $special = Region::factory()->special()->create();

    expect($geographical->isGeographical())->toBeTrue();
    expect($special->isGeographical())->toBeFalse();
});

test('getAllDescendants returns all nested children', function () {
    $grandparent = Region::factory()->create();
    $parent = Region::factory()->withParent($grandparent)->create();
    $child = Region::factory()->withParent($parent)->create();
    $sibling = Region::factory()->withParent($grandparent)->create();

    $descendants = $grandparent->getAllDescendants();

    expect($descendants)->toHaveCount(3);
    expect($descendants->pluck('id')->toArray())->toContain($parent->id, $child->id, $sibling->id);
});

test('country belongsToSpecialRegion works correctly', function () {
    $region = Region::factory()->special()->create();
    $country = Country::factory()->create();

    $now = Carbon::now();

    // Add country to region with current membership
    $region->specialCountries()->attach($country->id, [
        'start_date' => $now->copy()->subDays(30)->toDateString(),
        'end_date' => $now->copy()->addDays(30)->toDateString(),
    ]);

    expect($country->belongsToSpecialRegion($region, $now))->toBeTrue();
    expect($country->belongsToSpecialRegion($region, $now->copy()->addDays(60)))->toBeFalse();
});

test('country getSpecialRegionsAt returns correct regions', function () {
    $region1 = Region::factory()->special()->create();
    $region2 = Region::factory()->special()->create();
    $region3 = Region::factory()->special()->create();
    $country = Country::factory()->create();

    $now = Carbon::now();

    // Active in region1
    $region1->specialCountries()->attach($country->id, [
        'start_date' => $now->copy()->subDays(30)->toDateString(),
        'end_date' => null,
    ]);

    // Active in region2
    $region2->specialCountries()->attach($country->id, [
        'start_date' => $now->copy()->subDays(30)->toDateString(),
        'end_date' => $now->copy()->addDays(30)->toDateString(),
    ]);

    // Not active in region3 (ended)
    $region3->specialCountries()->attach($country->id, [
        'start_date' => $now->copy()->subDays(60)->toDateString(),
        'end_date' => $now->copy()->subDays(10)->toDateString(),
    ]);

    $activeRegions = $country->getSpecialRegionsAt($now);

    expect($activeRegions)->toHaveCount(2);
    expect($activeRegions->pluck('id')->toArray())->toContain($region1->id, $region2->id);
    expect($activeRegions->pluck('id')->toArray())->not->toContain($region3->id);
});
