<?php

namespace Eclipse\World\Jobs;

use Eclipse\Core\Models\User;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;
use Eclipse\World\Notifications\ImportFinishedNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ImportCountries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public bool $failOnTimeout = true;

    public ?int $userId;

    public string $locale;

    /**
     * Create a new job instance.
     */
    public function __construct(?int $userId, string $locale = 'en')
    {
        $this->userId = $userId;
        $this->locale = $locale;
    }

    public function handle(): void
    {
        Log::info('Starting countries import');

        $user = $this->userId ? User::find($this->userId) : null;

        try {
            // First, import/update regions
            $this->importRegions();

            // Load existing countries into an associative array
            $existingCountries = Country::withTrashed()->get()->keyBy('id');

            // Load new country data
            $countries = json_decode(file_get_contents('https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json'), true);

            if (! $countries) {
                throw new Exception('Failed to fetch or parse countries data');
            }

            foreach ($countries as $rawData) {
                if (! $rawData['independent']) {
                    continue;
                }

                $data = [
                    'id' => $rawData['cca2'],
                    'a3_id' => $rawData['cca3'],
                    'num_code' => $rawData['ccn3'],
                    'name' => $rawData['name']['common'],
                    'flag' => $rawData['flag'],
                    'region_id' => $this->getRegionIdForCountry($rawData),
                ];

                if (isset($existingCountries[$data['id']])) {
                    $existingCountries[$data['id']]->update($data);
                } else {
                    Country::create($data);
                }
            }

            // Seed special regions after countries are imported
            $this->seedSpecialRegions();

            Log::info('Countries import completed');
            if ($user) {
                $user->notify(new ImportFinishedNotification('success', 'countries', null, $this->locale));
            }
        } catch (Exception $e) {
            Log::error('Countries import failed: '.$e->getMessage());
            if ($user) {
                $user->notify(new ImportFinishedNotification('failed', 'countries', null, $this->locale));
            }
            throw $e;
        }
    }

    private function importRegions(): void
    {
        $geographicalRegions = $this->getGeographicalRegionsStructure();

        foreach ($geographicalRegions as $parentName => $subRegions) {
            $parent = Region::updateOrCreate(
                ['name' => $parentName, 'is_special' => false],
                ['code' => null, 'parent_id' => null]
            );

            foreach ($subRegions as $subRegionName) {
                Region::updateOrCreate(
                    ['name' => $subRegionName, 'is_special' => false],
                    ['code' => null, 'parent_id' => $parent->id]
                );
            }
        }
    }

    private function getGeographicalRegionsStructure(): array
    {
        return [
            'Africa' => [
                'Eastern Africa',
                'Middle Africa',
                'Northern Africa',
                'Southern Africa',
                'Western Africa',
            ],
            'Americas' => [
                'Caribbean',
                'Central America',
                'North America',
                'South America',
            ],
            'Asia' => [
                'Central Asia',
                'Eastern Asia',
                'South-Eastern Asia',
                'Southern Asia',
                'Western Asia',
            ],
            'Europe' => [
                'Central Europe',
                'Eastern Europe',
                'Northern Europe',
                'Southeast Europe',
                'Southern Europe',
                'Western Europe',
            ],
            'Oceania' => [
                'Australia and New Zealand',
                'Melanesia',
                'Micronesia',
                'Polynesia',
            ],
        ];
    }

    private function getRegionIdForCountry(array $countryData): ?int
    {
        if (! isset($countryData['subregion'])) {
            return null;
        }

        return Region::where('name', $countryData['subregion'])
            ->where('is_special', false)
            ->value('id');
    }

    private function seedSpecialRegions(): void
    {
        // Create EU special region
        $euRegion = Region::updateOrCreate(
            ['code' => 'EU'],
            [
                'name' => 'European Union',
                'is_special' => true,
            ]
        );

        $euMemberCountries = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
        ];

        $existingCountries = Country::whereIn('id', $euMemberCountries)->pluck('id');

        // Get current memberships
        $currentMemberships = $euRegion->specialCountries()
            ->wherePivot('start_date', '<=', Carbon::now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('world_country_in_special_region.end_date')
                    ->orWhere('world_country_in_special_region.end_date', '>=', Carbon::now()->toDateString());
            })
            ->pluck('world_countries.id')
            ->toArray();

        // Only update if there are changes needed
        $countriesToAdd = $existingCountries->diff($currentMemberships);
        $countriesToRemove = collect($currentMemberships)->diff($existingCountries);

        // Add new countries
        if ($countriesToAdd->isNotEmpty()) {
            $membershipData = $countriesToAdd->mapWithKeys(fn ($countryId) => [
                $countryId => [
                    'start_date' => Carbon::now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            $euRegion->specialCountries()->attach($membershipData);
        }

        // Remove countries that are no longer members
        if ($countriesToRemove->isNotEmpty()) {
            foreach ($countriesToRemove as $countryId) {
                $euRegion->specialCountries()
                    ->wherePivot('country_id', $countryId)
                    ->whereNull('world_country_in_special_region.end_date')
                    ->updateExistingPivot($countryId, [
                        'end_date' => Carbon::now()->subDay()->toDateString(),
                        'updated_at' => now(),
                    ]);
            }
        }
    }
}
