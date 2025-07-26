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
}
