<?php

use Eclipse\World\Filament\Clusters\World\Resources\CountryResource;
use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource;
use Eclipse\World\Filament\Clusters\World\Resources\PostResource;
use Eclipse\World\Filament\Clusters\World\Resources\RegionResource;

beforeEach(function () {
    $this->setUpSuperAdmin();
});

describe('Smoke Tests', function () {
    test('all resource URLs are accessible', function () {
        $resources = [
            CountryResource::class,
            CurrencyResource::class,
            PostResource::class,
            RegionResource::class,
        ];

        foreach ($resources as $resource) {
            $this->get($resource::getUrl())
                ->assertSuccessful()
                ->assertSee('Filament');
        }
    });

    test('country resource URLs are accessible', function () {
        $this->get(CountryResource::getUrl())
            ->assertSuccessful()
            ->assertSee('Countries')
            ->assertSee('Filament');
    });

    test('currency resource URLs are accessible', function () {
        $this->get(CurrencyResource::getUrl())
            ->assertSuccessful()
            ->assertSee('Currencies')
            ->assertSee('Filament');
    });

    test('post resource URLs are accessible', function () {
        $this->get(PostResource::getUrl())
            ->assertSuccessful()
            ->assertSee('Posts')
            ->assertSee('Filament');
    });

    test('region resource URLs are accessible', function () {
        $this->get(RegionResource::getUrl())
            ->assertSuccessful()
            ->assertSee('Regions')
            ->assertSee('Filament');
    });

    test('all resource URLs return valid HTML', function () {
        $resources = [
            CountryResource::class,
            CurrencyResource::class,
            PostResource::class,
            RegionResource::class,
        ];

        foreach ($resources as $resource) {
            $response = $this->get($resource::getUrl());

            $response->assertSuccessful();
            $response->assertHeader('content-type', 'text/html; charset=UTF-8');
            $response->assertSee('<!DOCTYPE html>', false);
        }
    });

    test('all resource URLs have no JavaScript errors', function () {
        $resources = [
            CountryResource::class,
            CurrencyResource::class,
            PostResource::class,
            RegionResource::class,
        ];

        foreach ($resources as $resource) {
            $response = $this->get($resource::getUrl());

            $response->assertSuccessful();
            $response->assertDontSee('Uncaught');
            $response->assertDontSee('ReferenceError');
            $response->assertDontSee('TypeError');
        }
    });
});
