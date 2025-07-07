<?php

namespace Eclipse\World\Console\Commands;

use Eclipse\World\Jobs\ImportPosts;
use Illuminate\Console\Command;

class ImportPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'world:import-post {country : The country code (SI or HR)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import posts for a specific country (SI or HR)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $country = strtoupper($this->argument('country'));

        if (! in_array($country, ['SI', 'HR'])) {
            $this->error('Invalid country code. Only SI and HR are supported.');

            return self::FAILURE;
        }

        $this->info("Dispatching import job for country: {$country}");

        ImportPosts::dispatch($country);

        $this->info('Import job has been queued successfully!');
        $this->comment('The import will run in the background. Check the logs for progress.');

        return self::SUCCESS;
    }
}
