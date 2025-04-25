<?php

namespace Eclipse\World\Jobs;

use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ImportCountries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public bool $failOnTimeout = true;

    public function handle(): void
    {
        $existingCountries = Country::withTrashed()->get()->keyBy('id');

        $countries = json_decode(file_get_contents(
            'https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json'
        ), true);

        $geoRegionMap = [];
        $euMembers = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE',
            'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT',
            'RO', 'SK', 'SI', 'ES', 'SE'
        ];

        $euRegion = Region::firstOrCreate(
            ['code' => 'EU'],
            ['name' => 'European Union', 'is_special' => true]
        );

        foreach ($countries as $rawData) {
            if (! $rawData['independent']) {
                continue;
            }

            $geoRegionName = $rawData['region'] ?? null;
            $subRegionName = $rawData['subregion'] ?? null;

            $parent = null;

            if ($geoRegionName) {
                $parent = $geoRegionMap[$geoRegionName] ?? Region::firstOrCreate(
                    ['name' => $geoRegionName],
                    ['is_special' => false]
                );
                $geoRegionMap[$geoRegionName] = $parent;
            }

            $region = $parent;

            if ($subRegionName) {
                $region = $geoRegionMap[$subRegionName] ?? Region::firstOrCreate(
                    ['name' => $subRegionName],
                    ['parent_id' => $parent?->id, 'is_special' => false]
                );
                $geoRegionMap[$subRegionName] = $region;
            }

            $data = [
                'id' => $rawData['cca2'],
                'a3_id' => $rawData['cca3'],
                'num_code' => $rawData['ccn3'],
                'name' => $rawData['name']['common'],
                'flag' => $rawData['flag'],
                'region_id' => $region?->id,
            ];

            $country = $existingCountries[$data['id']] ?? null;

            if ($country) {
                $country->update($data);
            } else {
                $country = Country::create($data);
            }

            if (in_array($country->id, $euMembers)) {
                $country->specialRegions()->syncWithoutDetaching([
                    $euRegion->id => ['start_date' => Carbon::now()]
                ]);
            }
        }
    }
}
