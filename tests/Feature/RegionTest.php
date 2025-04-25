<?php

use Eclipse\World\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a region', function () {
    $region = Region::factory()->create([
        'name' => 'Europe',
        'code' => 'EU',
        'is_special' => false,
    ]);

    expect($region)->toBeInstanceOf(Region::class)
        ->and($region->name)->toBe('Europe')
        ->and($region->is_special)->toBeFalse();
});

it('can have subregions', function () {
    $parent = Region::factory()->create(['name' => 'Africa']);
    $subregion = Region::factory()->create(['name' => 'West Africa', 'parent_id' => $parent->id]);

    expect($parent->subRegions)->toHaveCount(1)
        ->and($subregion->parent->id)->toBe($parent->id);
});
