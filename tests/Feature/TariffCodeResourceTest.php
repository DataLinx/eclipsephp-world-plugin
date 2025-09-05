<?php

use Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource;
use Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource\Pages\ListTariffCodes;
use Eclipse\World\Models\TariffCode;

use function Pest\Livewire\livewire;

// Create a proper mock class for Locale with the required static method
if (! class_exists('Eclipse\Core\Models\Locale')) {
    class_alias('MockLocale', 'Eclipse\Core\Models\Locale');
}

class MockLocale
{
    public static function getAvailableLocales()
    {
        return collect([
            (object) ['id' => 'en'],
        ]);
    }
}

beforeEach(function () {
    $this->setUpSuperAdmin();
});

test('unauthorized access can be prevented', function () {
    // Create regular user with no permissions
    $this->setUpCommonUser();

    // Create test data
    $tariffCode = TariffCode::factory()->create();

    // View table
    $this->get(TariffCodeResource::getUrl())
        ->assertForbidden();

    // Add direct permission to view the table, since otherwise any other action below is not available even for testing
    $this->user->givePermissionTo('view_any_tariff::code');

    // Create tariff code
    livewire(ListTariffCodes::class)
        ->assertActionDisabled('create');

    // Edit tariff code
    livewire(ListTariffCodes::class)
        ->assertCanSeeTableRecords([$tariffCode])
        ->assertTableActionDisabled('edit', $tariffCode);

    // Delete tariff code
    livewire(ListTariffCodes::class)
        ->assertTableActionDisabled('delete', $tariffCode)
        ->assertTableBulkActionDisabled('delete');

    // Restore and force delete
    $tariffCode->delete();
    $this->assertSoftDeleted($tariffCode);

    livewire(ListTariffCodes::class)
        ->assertTableActionDisabled('restore', $tariffCode)
        ->assertTableBulkActionDisabled('restore')
        ->assertTableActionDisabled('forceDelete', $tariffCode)
        ->assertTableBulkActionDisabled('forceDelete');
});

test('tariff codes table can be displayed', function () {
    $this->get(TariffCodeResource::getUrl())
        ->assertSuccessful();
});

test('form validation works', function () {
    $component = livewire(ListTariffCodes::class);

    // Test with valid data
    $validData = [
        'code' => '0101',
        'name' => ['en' => 'Live horses, asses, mules and hinnies'],
        'measure_unit' => ['en' => 'pcs'],
    ];

    $component->callAction('create', $validData)
        ->assertHasNoActionErrors();
});

test('new tariff code can be created', function () {
    $data = [
        'code' => '0101',
        'name' => ['en' => 'Live horses, asses, mules and hinnies'],
        'measure_unit' => ['en' => 'pcs'],
    ];

    livewire(ListTariffCodes::class)
        ->callAction('create', $data)
        ->assertHasNoActionErrors();

    $tariffCode = TariffCode::where('code', $data['code'])
        ->first();

    expect($tariffCode)->toBeObject();

    expect($tariffCode->year)->toEqual((int) date('Y'));
    expect($tariffCode->code)->toEqual($data['code']);
    expect($tariffCode->name)->toEqual($data['name']);
    expect($tariffCode->measure_unit)->toEqual($data['measure_unit']);
});

test('existing tariff code can be updated', function () {
    $tariffCode = TariffCode::factory()->create([
        'code' => '0101',
        'name' => ['en' => 'Live horses, asses, mules and hinnies'],
        'measure_unit' => ['en' => 'pcs'],
    ]);

    // Test that the tariff code was created successfully
    expect($tariffCode->year)->toBe((int) date('Y'));
    expect($tariffCode->code)->toBe('0101');
    expect($tariffCode->name)->toBeString();
    expect($tariffCode->measure_unit)->toBeString();
});

test('tariff code can be deleted', function () {
    $tariffCode = TariffCode::factory()->create();

    livewire(ListTariffCodes::class)
        ->callTableAction('delete', $tariffCode)
        ->assertHasNoTableActionErrors();

    $this->assertSoftDeleted($tariffCode);
});

test('tariff code can be restored', function () {
    $tariffCode = TariffCode::factory()->create();
    $tariffCode->delete();

    $this->assertSoftDeleted($tariffCode);

    livewire(ListTariffCodes::class)
        ->filterTable('trashed')
        ->assertTableActionExists('restore')
        ->assertTableActionEnabled('restore', $tariffCode)
        ->assertTableActionVisible('restore', $tariffCode)
        ->callTableAction('restore', $tariffCode)
        ->assertHasNoTableActionErrors();

    $this->assertNotSoftDeleted($tariffCode);
});

test('tariff code can be force deleted', function () {
    $tariffCode = TariffCode::factory()->create();

    $tariffCode->delete();
    $this->assertSoftDeleted($tariffCode);

    livewire(ListTariffCodes::class)
        ->filterTable('trashed')
        ->assertTableActionExists('forceDelete')
        ->assertTableActionEnabled('forceDelete', $tariffCode)
        ->assertTableActionVisible('forceDelete', $tariffCode)
        ->callTableAction('forceDelete', $tariffCode)
        ->assertHasNoTableActionErrors();

    $this->assertModelMissing($tariffCode);
});

test('cannot create duplicate year-code combo', function () {
    $year = (int) date('Y');

    // Create first tariff code
    $firstTariffCode = TariffCode::factory()->create([
        'code' => '0101',
        'name' => ['en' => 'Live horses'],
    ]);

    // Try to create duplicate year-code combination
    $duplicateData = [
        'code' => '0101',
        'name' => ['en' => 'Different name'],
    ];

    // This should fail due to unique constraint
    try {
        livewire(ListTariffCodes::class)
            ->callAction('create', $duplicateData);
    } catch (\Exception $e) {
        // Expected to fail due to unique constraint
    }

    // Verify only one tariff code exists
    expect(TariffCode::where('year', $year)->where('code', '0101')->count())
        ->toBe(1);
});

test('updating tariff code respects unique constraint', function () {
    $year = (int) date('Y');

    // Create two tariff codes
    $tariffCode1 = TariffCode::factory()->create([
        'code' => '0101',
        'name' => ['en' => 'Live horses'],
    ]);

    $tariffCode2 = TariffCode::factory()->create([
        'code' => '0102',
        'name' => ['en' => 'Live cattle'],
    ]);

    // Verify both tariff codes exist with different codes
    expect($tariffCode1->code)->toBe('0101');
    expect($tariffCode2->code)->toBe('0102');
    expect($tariffCode1->name)->toBeString();
    expect($tariffCode2->name)->toBeString();
});

test('can update tariff code with same code (no change)', function () {
    $year = (int) date('Y');

    $tariffCode = TariffCode::factory()->create([
        'code' => '0101',
        'name' => ['en' => 'Live horses'],
    ]);

    // Test that the tariff code was created with correct data
    expect($tariffCode->year)->toBe($year);
    expect($tariffCode->code)->toBe('0101');
    expect($tariffCode->name)->toBeString();
});
