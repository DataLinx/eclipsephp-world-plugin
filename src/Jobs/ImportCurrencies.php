<?php

namespace Eclipse\World\Jobs;

use Eclipse\Core\Models\User;
use Eclipse\World\Models\Currency;
use Eclipse\World\Notifications\ImportFinishedNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportCurrencies implements ShouldQueue
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
        Log::info('Starting currencies import');

        $user = $this->userId ? User::find($this->userId) : null;

        try {
            // Load existing currencies into an associative array
            $existingCurrencies = Currency::withTrashed()->get()->keyBy('id');

            // Load new currency data from REST Countries API
            $countries = json_decode(file_get_contents('https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json'), true);

            if (! $countries) {
                throw new Exception('Failed to fetch or parse countries data');
            }

            $processedCurrencies = [];

            foreach ($countries as $rawData) {
                if (! $rawData['independent'] || empty($rawData['currencies'])) {
                    continue;
                }

                foreach ($rawData['currencies'] as $currencyCode => $currencyData) {
                    // Skip if we've already processed this currency
                    if (isset($processedCurrencies[$currencyCode])) {
                        continue;
                    }

                    $data = [
                        'id' => $currencyCode,
                        'name' => $currencyData['name'],
                        'is_active' => true,
                    ];

                    if (isset($existingCurrencies[$currencyCode])) {
                        $existingCurrencies[$currencyCode]->update($data);
                    } else {
                        Currency::create($data);
                    }

                    $processedCurrencies[$currencyCode] = true;
                }
            }

            Log::info('Currencies import completed');
            if ($user) {
                $user->notify(new ImportFinishedNotification('success', 'currencies', null, $this->locale));
            }
        } catch (Exception $e) {
            Log::error('Currencies import failed: '.$e->getMessage());
            if ($user) {
                $user->notify(new ImportFinishedNotification('failed', 'currencies', null, $this->locale));
            }
            throw $e;
        }
    }
}
