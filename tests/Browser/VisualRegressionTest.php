<?php

use Eclipse\World\Filament\Clusters\World\Resources\CountryResource;
use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource;
use Eclipse\World\Filament\Clusters\World\Resources\PostResource;
use Eclipse\World\Filament\Clusters\World\Resources\RegionResource;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;

beforeEach(function () {
    $this->setUpSuperAdmin();
});

describe('Visual Regression Tests', function () {
    test('country resource page visual regression', function () {
        $region = Region::factory()->create(['name' => 'Visual Test Region']);
        $country = Country::factory()->create([
            'name' => 'Visual Test Country',
            'id' => 'VT',
            'a3_id' => 'VTC',
            'num_code' => '999',
            'flag' => '🏳️',
            'region_id' => $region->id,
        ]);

        $this->visit(CountryResource::getUrl())
            ->assertSee('Countries')
            ->assertSee('Visual Test Country')
            ->wait(1)
            ->assertScreenshotMatches();
    });

    test('currency resource page visual regression', function () {
        $this->visit(CurrencyResource::getUrl())
            ->assertSee('Currencies')
            ->wait(1)
            ->assertScreenshotMatches();
    });

    test('post resource page visual regression', function () {
        $this->visit(PostResource::getUrl())
            ->assertSee('Posts')
            ->wait(1)
            ->assertScreenshotMatches();
    });

    test('region resource page visual regression', function () {
        $this->visit(RegionResource::getUrl())
            ->assertSee('Regions')
            ->wait(1)
            ->assertScreenshotMatches();
    });

    test('country resource page with data visual regression', function () {
        $region1 = Region::factory()->create(['name' => 'Europe']);
        $region2 = Region::factory()->create(['name' => 'Asia']);

        Country::factory()->create([
            'name' => 'Germany',
            'id' => 'DE',
            'a3_id' => 'DEU',
            'num_code' => '276',
            'flag' => '🇩🇪',
            'region_id' => $region1->id,
        ]);

        Country::factory()->create([
            'name' => 'Japan',
            'id' => 'JP',
            'a3_id' => 'JPN',
            'num_code' => '392',
            'flag' => '🇯🇵',
            'region_id' => $region2->id,
        ]);

        Country::factory()->create([
            'name' => 'United States',
            'id' => 'US',
            'a3_id' => 'USA',
            'num_code' => '840',
            'flag' => '🇺🇸',
            'region_id' => $region1->id,
        ]);

        $this->visit(CountryResource::getUrl())
            ->assertSee('Countries')
            ->assertSee('Germany')
            ->assertSee('Japan')
            ->assertSee('United States')
            ->wait(1)
            ->assertScreenshotMatches();
    });
});
