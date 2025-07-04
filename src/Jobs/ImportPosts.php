<?php

namespace Eclipse\World\Jobs;

use Eclipse\World\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const string OPENDATASOFT_RECORDS_API_URL = 'https://data.opendatasoft.com/api/records/1.0/';

    public string $countryId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!in_array($this->countryId, ['SI', 'HR'])) {
            throw new Exception("Country {$this->countryId} not supported for import");
        }

        $batchSize = 1000;
        $offset = 0;
        $processedCodes = [];

        Log::info("Starting postal data import for country: {$this->countryId}");

        do {
            [$totalRecords, $records] = $this->getData($batchSize, $offset);

            foreach ($records as $record) {
                [$postalCode, $placeName] = $this->getRecordData($record);

                if (array_key_exists($postalCode, $processedCodes)) {
                    continue;
                }

                $processedCodes[$postalCode] = true;

                $existingPost = Post::where('country_id', $this->countryId)
                    ->where('code', $postalCode)
                    ->first();

                if (empty($existingPost)) {
                    Post::create([
                        'country_id' => $this->countryId,
                        'code' => $postalCode,
                        'name' => $placeName,
                    ]);
                } elseif ($existingPost->name !== $placeName) {
                    $existingPost->update(['name' => $placeName]);
                }
            }

            $offset += $batchSize;
        } while ($offset < $totalRecords);

        Log::info("Postal data import completed for {$this->countryId}");
    }

    /**
     * Get data from the external API
     */
    private function getData(int $batchSize, int $offset): array
    {
        $url = self::OPENDATASOFT_RECORDS_API_URL
            . "search/?dataset=geonames-postal-code@public"
            . "&q="
            . "&rows={$batchSize}"
            . "&start={$offset}"
            . "&sort=postal_code"
            . "&refine.country_code={$this->countryId}";

        $response = Http::get($url);

        if (!$response->successful()) {
            throw new Exception("Failed to fetch data from Opendatasoft API: " . $response->status());
        }

        $data = $response->json();

        if (empty($data)) {
            throw new Exception('Empty data set received from Opendatasoft API');
        }

        return [
            $data['nhits'],
            $data['records'],
        ];
    }

    /**
     * Extract code and name from record based on country
     */
    private function getRecordData(array $record): array
    {
        switch ($this->countryId) {
            case 'HR':
                return [
                    $record['fields']['postal_code'],
                    $record['fields']['admin_name3'],
                ];

            case 'SI':
                return [
                    $record['fields']['postal_code'],
                    $record['fields']['place_name'],
                ];

            default:
                throw new Exception("Country {$this->countryId} not supported");
        }
    }
}