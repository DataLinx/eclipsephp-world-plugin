<?php

namespace Eclipse\World\Jobs;

use Eclipse\Common\Foundation\Jobs\QueueableJob;
use Eclipse\World\Models\Currency;
use Exception;

class ImportCurrencies extends QueueableJob
{
    public int $timeout = 60;

    public bool $failOnTimeout = true;

    protected function execute(): void
    {
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
    }

    protected function getJobName(): string
    {
        return __('eclipse-world::currencies.import.job_name', [], $this->locale);
    }

    protected function getNotificationTitle(): string
    {
        return __("eclipse-world::currencies.notifications.{$this->status->value}.title", [], $this->locale);
    }
}
