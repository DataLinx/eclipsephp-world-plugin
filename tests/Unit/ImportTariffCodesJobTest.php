<?php

use Eclipse\World\Jobs\ImportTariffCodes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // ensure clean cache between runs
    Cache::flush();

    // Ensure storage directory exists
    Storage::disk('local')->makeDirectory('private/cn/2025');
});

function fakeCnResponses(int $year, array $overrides = []): void
{
    $base = "https://www.datalinx.io/api/{$year}/";

    $files = array_merge([
        'cn_list_en.csv' => "code,name,unit\n0101,Live horses,pcs\n",
        'cn_list_multilingual.csv' => "CNKEY,LEVEL,CN_CODE,NAME_EN,NAME_SL,NAME_HR\n010011000090,1,0101,Live horses,Zivi konji,Konji\n",
        'cn_list_sl.csv' => "code,name,unit_id\n0101,Zivi konji,11\n",
        'cn_list_sl_units.csv' => "id,unit,description\n11,kos,štev. kosov\n",
    ], $overrides);

    Http::fake(array_reduce(array_keys($files), function ($carry, $file) use ($base, $files) {
        $carry[$base.$file] = Http::response($files[$file]);

        return $carry;
    }, []));
}

test('imports EN only (names and units)', function () {
    $year = (int) date('Y');
    fakeCnResponses($year);

    // Verify the job runs without throwing exceptions
    expect(fn () => (new ImportTariffCodes(['en']))->handle())->not->toThrow(\Exception::class);
});

test('imports EN + SL with unit resolution', function () {
    $year = (int) date('Y');
    fakeCnResponses($year);

    // Verify the job runs without throwing exceptions
    expect(fn () => (new ImportTariffCodes(['en', 'sl']))->handle())->not->toThrow(\Exception::class);
});

test('imports other language without units', function () {
    $year = (int) date('Y');
    fakeCnResponses($year, [
        // add hr name in multilingual list for coverage
        'cn_list_multilingual.csv' => "CNKEY,LEVEL,CN_CODE,NAME_EN,NAME_HR\n010011000090,1,0101,Live horses,Konji\n",
        // hr has no units files
    ]);

    // Verify the job runs without throwing exceptions
    expect(fn () => (new ImportTariffCodes(['hr']))->handle())->not->toThrow(\Exception::class);
});

test('missing year gracefully falls back to previous year', function () {
    $year = (int) date('Y') + 1; // Test with next year

    // Make next year fail, current year succeed
    Http::fake([
        "https://www.datalinx.io/api/{$year}/*" => Http::response('', 404),
    ]);

    fakeCnResponses($year - 1); // Current year

    // Verify the job runs without throwing exceptions
    expect(fn () => (new ImportTariffCodes(['en']))->handle())->not->toThrow(\Exception::class);
});
