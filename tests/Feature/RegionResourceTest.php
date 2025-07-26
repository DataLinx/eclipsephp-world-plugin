<?php

use Eclipse\World\Filament\Clusters\World\Resources\RegionResource;
use Eclipse\World\Filament\Clusters\World\Resources\RegionResource\Pages\ListRegions;
use Eclipse\World\Models\Region;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->setUpSuperAdmin();
});

test('unauthorized access can be prevented', function () {
    // Create regular user with no permissions
    $this->setUpCommonUser();

    // Create test region
    $region = Region::factory()->create();

    // View table
    $this->get(RegionResource::getUrl())
        ->assertForbidden();

    // Add direct permission to view the table, since otherwise any other action below is not available even for testing
    $this->user->givePermissionTo('view_any_region');

    // Create region
    livewire(ListRegions::class)
        ->assertActionDisabled('create');

    // Edit region
    livewire(ListRegions::class)
        ->assertCanSeeTableRecords([$region])
        ->assertTableActionDisabled('edit', $region);

    // Delete region
    livewire(ListRegions::class)
        ->assertTableActionDisabled('delete', $region)
        ->assertTableBulkActionDisabled('delete');

    // Restore and force delete
    $region->delete();
    $this->assertSoftDeleted($region);

    livewire(ListRegions::class)
        ->assertTableActionDisabled('restore', $region)
        ->assertTableBulkActionDisabled('restore')
        ->assertTableActionDisabled('forceDelete', $region)
        ->assertTableBulkActionDisabled('forceDelete');
});

test('regions table can be displayed', function () {
    $this->get(RegionResource::getUrl())
        ->assertSuccessful();
});

test('form validation works', function () {
    $component = livewire(ListRegions::class);

    // Test required fields
    $component->callAction('create')
        ->assertHasActionErrors([
            'name' => 'required',
        ]);

    // Test with valid data
    $component->callAction('create', Region::factory()->definition())
        ->assertHasNoActionErrors();
});

test('new region can be created', function () {
    $data = Region::factory()->definition();

    livewire(ListRegions::class)
        ->callAction('create', $data)
        ->assertHasNoActionErrors();

    $region = Region::where('name', $data['name'])->first();
    expect($region)->toBeObject();

    foreach ($data as $key => $val) {
        expect($region->$key)->toEqual($val);
    }
});

test('existing region can be updated', function () {
    $region = Region::factory()->create([
        'name' => 'Test Region',
        'code' => 'TR',
        'is_special' => false,
    ]);

    $data = [
        'name' => 'Updated Region',
        'code' => 'UR',
        'is_special' => true,
    ];

    livewire(ListRegions::class)
        ->callTableAction('edit', $region, $data)
        ->assertHasNoTableActionErrors();

    $region->refresh();

    foreach ($data as $key => $val) {
        expect($region->$key)->toEqual($val);
    }
});

test('region can be deleted', function () {
    $region = Region::factory()->create();

    livewire(ListRegions::class)
        ->callTableAction('delete', $region)
        ->assertHasNoTableActionErrors();

    $this->assertSoftDeleted($region);
});

test('region can be restored', function () {
    $region = Region::factory()->create();
    $region->delete();

    $this->assertSoftDeleted($region);

    livewire(ListRegions::class)
        ->filterTable('trashed')
        ->assertTableActionExists('restore')
        ->assertTableActionEnabled('restore', $region)
        ->assertTableActionVisible('restore', $region)
        ->callTableAction('restore', $region)
        ->assertHasNoTableActionErrors();

    $this->assertNotSoftDeleted($region);
});

test('region can be force deleted', function () {
    $region = Region::factory()->create();

    $region->delete();
    $this->assertSoftDeleted($region);

    livewire(ListRegions::class)
        ->filterTable('trashed')
        ->assertTableActionExists('forceDelete')
        ->assertTableActionEnabled('forceDelete', $region)
        ->assertTableActionVisible('forceDelete', $region)
        ->callTableAction('forceDelete', $region)
        ->assertHasNoTableActionErrors();

    $this->assertModelMissing($region);
});

test('region with parent can be created', function () {
    $parent = Region::factory()->create();
    $data = Region::factory()->withParent($parent)->make()->toArray();

    livewire(ListRegions::class)
        ->callAction('create', $data)
        ->assertHasNoActionErrors();

    $region = Region::where('name', $data['name'])->first();
    expect($region)->toBeObject();
    expect($region->parent_id)->toEqual($parent->id);
});

test('special region can be created', function () {
    $data = Region::factory()->special()->make()->toArray();

    livewire(ListRegions::class)
        ->callAction('create', $data)
        ->assertHasNoActionErrors();

    $region = Region::where('name', $data['name'])->first();
    expect($region)->toBeObject();
    expect($region->is_special)->toBeTrue();
});

test('regions can be filtered by type', function () {
    $geographical = Region::factory()->geographical()->create();
    $special = Region::factory()->special()->create();

    // Filter by geographical
    livewire(ListRegions::class)
        ->filterTable('is_special', '0')
        ->assertCanSeeTableRecords([$geographical])
        ->assertCanNotSeeTableRecords([$special]);

    // Filter by special
    livewire(ListRegions::class)
        ->filterTable('is_special', '1')
        ->assertCanSeeTableRecords([$special])
        ->assertCanNotSeeTableRecords([$geographical]);
});

test('regions can be filtered by parent', function () {
    $parent = Region::factory()->create();
    $child = Region::factory()->withParent($parent)->create();
    $orphan = Region::factory()->create();

    livewire(ListRegions::class)
        ->filterTable('parent_id', $parent->id)
        ->assertCanSeeTableRecords([$child])
        ->assertCanNotSeeTableRecords([$parent, $orphan]);
});
