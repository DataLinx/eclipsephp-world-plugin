<?php

namespace Eclipse\World\Console\Commands;

use Eclipse\Core\Models\Locale;
use Eclipse\World\Jobs\ImportTariffCodes;
use Illuminate\Console\Command;

class ImportTariffCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'world:import-tariff-codes {--locales=* : Locales to import (defaults to available panel locales)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CN tariff codes and units from DataLinx public datasets.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $locales = (array) $this->option('locales');
        if (empty($locales)) {
            $available = Locale::getAvailableLocales()->pluck('id')->toArray();
            $selected = $this->choice('Select locales to import (comma-select with multiple)', $available, null, null, true);
            $locales = $selected ?: $available;
        }

        $this->info('Importing tariff codes for locales: '.implode(', ', $locales));

        ImportTariffCodes::dispatchSync($locales);

        $this->info('Tariff codes import completed.');
    }
}
