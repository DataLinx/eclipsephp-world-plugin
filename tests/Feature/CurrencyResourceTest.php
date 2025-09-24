<?php

use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource;
use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource\Pages\ListCurrencies;
use Eclipse\World\Models\Currency;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Arr;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->setUpSuperAdmin();
});

test('unauthorized access can be prevented', function () {
    // Create regular user with no permissions
    $this->setUpCommonUser();

    // Create test currency
    $currency = Currency::factory()->create();

    // View table
    $this->get(CurrencyResource::getUrl())
        ->assertForbidden();

    // Add direct permission to view the table, since otherwise any other action below is not available even for testing
    $this->user->givePermissionTo('view_any_currency');

    // Create currency
    livewire(ListCurrencies::class)
        ->assertActionDisabled('create');

    // Edit currency
    livewire(ListCurrencies::class)
        ->assertCanSeeTableRecords([$currency])
        ->assertTableActionDisabled('edit', $currency);

    // Delete currency
    livewire(ListCurrencies::class)
        ->assertTableActionDisabled('delete', $currency)
        ->assertTableBulkActionDisabled('delete');

    $currency->delete();
    $this->assertSoftDeleted($currency);
});

test('currencies table can be displayed', function () {
    $this->get(CurrencyResource::getUrl())
        ->assertSuccessful();
});

test('form validation works', function () {
    // Submit with empty data
    livewire(ListCurrencies::class)
        ->callAction(TestAction::make('create'))
        ->assertHasFormErrors([
            'id' => ['required'],
            'name' => ['required'],
        ]);

    // Submit with valid data
    livewire(ListCurrencies::class)
        ->callAction(
            TestAction::make('create'),
            data: Currency::factory()->definition(),
        )
        ->assertHasNoFormErrors();
});

test('new currency can be created', function () {
    $data = Currency::factory()->definition();

    livewire(ListCurrencies::class)
        ->callAction('create', $data)
        ->assertHasNoActionErrors();

    $currency = Currency::where('id', $data['id'])->first();
    expect($currency)->toBeObject();

    foreach ($data as $key => $val) {
        expect($currency->$key)->toEqual($val);
    }
});

test('existing currency can be updated', function () {
    $currency = Currency::factory()->create([
        'id' => 'USD',
        'name' => 'US Dollar',
        'is_active' => true,
    ]);

    $data = Arr::except(Currency::factory()->definition(), ['id']);

    livewire(ListCurrencies::class)
        ->callTableAction('edit', $currency, $data)
        ->assertHasNoTableActionErrors();

    $currency->refresh();

    foreach ($data as $key => $val) {
        expect($currency->$key)->toEqual($val);
    }
});

test('currency can be deleted', function () {
    $currency = Currency::factory()->create();

    livewire(ListCurrencies::class)
        ->callTableAction('delete', $currency)
        ->assertHasNoTableActionErrors();

    $this->assertSoftDeleted($currency);
});

test('currency can be restored', function () {
    $currency = Currency::factory()->create();
    $currency->delete();

    $this->assertSoftDeleted($currency);

    livewire(ListCurrencies::class)
        ->filterTable('trashed')
        ->assertTableActionExists('restore')
        ->assertTableActionEnabled('restore', $currency)
        ->assertTableActionVisible('restore', $currency)
        ->callTableAction('restore', $currency)
        ->assertHasNoTableActionErrors();

    $this->assertNotSoftDeleted($currency);
});

test('currency can be force deleted', function () {
    $currency = Currency::factory()->create();

    $currency->delete();
    $this->assertSoftDeleted($currency);

    livewire(ListCurrencies::class)
        ->filterTable('trashed')
        ->assertTableActionExists('forceDelete')
        ->assertTableActionEnabled('forceDelete', $currency)
        ->assertTableActionVisible('forceDelete', $currency)
        ->callTableAction('forceDelete', $currency)
        ->assertHasNoTableActionErrors();

    $this->assertModelMissing($currency);
});
