<?php

use Eclipse\World\Jobs\ImportCountries;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;
use Illuminate\Support\Facades\Http;

test('import countries job creates regions', function () {
    // Mock the HTTP response
    Http::fake([
        'https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json' => Http::response([
            [
                'cca2' => 'US',
                'cca3' => 'USA',
                'ccn3' => '840',
                'name' => ['common' => 'United States'],
                'flag' => '🇺🇸',
                'independent' => true,
                'region' => 'Americas',
                'subregion' => 'North America',
            ],
            [
                'cca2' => 'DE',
                'cca3' => 'DEU',
                'ccn3' => '276',
                'name' => ['common' => 'Germany'],
                'flag' => '🇩🇪',
                'independent' => true,
                'region' => 'Europe',
                'subregion' => 'Western Europe',
            ],
        ]),
    ]);

    $job = new ImportCountries(null);
    $job->handle();

    // Check that regions were created
    expect(Region::where('name', 'Americas')->exists())->toBeTrue();
    expect(Region::where('name', 'Europe')->exists())->toBeTrue();
    expect(Region::where('name', 'North America')->exists())->toBeTrue();
    expect(Region::where('name', 'Western Europe')->exists())->toBeTrue();

    // Check parent-child relationships
    $americas = Region::where('name', 'Americas')->first();
    $northAmerica = Region::where('name', 'North America')->first();
    expect($northAmerica->parent_id)->toEqual($americas->id);

    $europe = Region::where('name', 'Europe')->first();
    $westernEurope = Region::where('name', 'Western Europe')->first();
    expect($westernEurope->parent_id)->toEqual($europe->id);
});

test('import countries job assigns regions to countries', function () {
    // Mock the HTTP response
    Http::fake([
        'https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json' => Http::response([
            [
                'cca2' => 'US',
                'cca3' => 'USA',
                'ccn3' => '840',
                'name' => ['common' => 'United States'],
                'flag' => '🇺🇸',
                'independent' => true,
                'region' => 'Americas',
                'subregion' => 'North America',
            ],
        ]),
    ]);

    $job = new ImportCountries(null);
    $job->handle();

    $country = Country::where('id', 'US')->first();
    expect($country)->toBeObject();
    expect($country->region)->toBeInstanceOf(Region::class);
    expect($country->region->name)->toEqual('North America');
});

test('import countries job handles countries without subregion', function () {
    // Create a country directly to test the region assignment logic
    $countryData = [
        'cca2' => 'XX',
        'cca3' => 'XXX',
        'ccn3' => '999',
        'name' => ['common' => 'Test Country'],
        'flag' => '🏳️',
        'independent' => true,
        'region' => 'Test Region',
        // No subregion field
    ];

    // Test the region assignment logic directly
    $job = new ImportCountries(null);
    $reflection = new ReflectionClass($job);
    $method = $reflection->getMethod('getRegionIdForCountry');
    $method->setAccessible(true);

    $regionId = $method->invoke($job, $countryData);
    expect($regionId)->toBeNull();

    // Also test that a country can be created with null region_id
    $country = Country::create([
        'id' => 'XX',
        'a3_id' => 'XXX',
        'num_code' => '999',
        'name' => 'Test Country',
        'flag' => '🏳️',
        'region_id' => null,
    ]);

    expect($country->region_id)->toBeNull();
});

test('import countries job updates existing countries', function () {
    // Create existing country
    $existingCountry = Country::factory()->create([
        'id' => 'US',
        'name' => 'Old Name',
    ]);

    // Mock the HTTP response
    Http::fake([
        'https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json' => Http::response([
            [
                'cca2' => 'US',
                'cca3' => 'USA',
                'ccn3' => '840',
                'name' => ['common' => 'United States'],
                'flag' => '🇺🇸',
                'independent' => true,
                'region' => 'Americas',
                'subregion' => 'North America',
            ],
        ]),
    ]);

    $job = new ImportCountries(null);
    $job->handle();

    $existingCountry->refresh();
    expect($existingCountry->name)->toEqual('United States');
    expect($existingCountry->region)->toBeInstanceOf(Region::class);
    expect($existingCountry->region->name)->toEqual('North America');
});
