<?php

namespace Eclipse\World\Jobs;

use Eclipse\Common\Foundation\Jobs\QueueableJob;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ImportTariffCodes extends QueueableJob
{
    /**
     * The timeout for the job.
     */
    public int $timeout = 300;

    /**
     * Whether to fail the job on timeout.
     */
    public bool $failOnTimeout = true;

    /**
     * The locales to import.
     *
     * @var array<string>
     */
    private array $locales;

    /**
     * Create a new job instance.
     *
     * @param  array<string>  $locales
     */
    public function __construct(array $locales)
    {
        parent::__construct();
        $this->locales = $locales;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    protected function execute(): void
    {
        $year = (int) date('Y');

        $files = $this->ensureYearCsvsAndReturnPaths($year)
            ?? $this->ensureYearCsvsAndReturnPaths($year - 1);

        if (! $files) {
            throw new Exception('CN files not available for current or previous year.');
        }

        [$enPath, $multiPath, $slPath, $slUnitsPath] = $files;

        $enReader = $this->csv($enPath);
        $englishUnitsByCode = [];
        foreach ($enReader->getRecords() as $row) {
            $code = isset($row['code']) ? trim($row['code']) : null;
            $unit = isset($row['unit']) ? trim((string) $row['unit']) : null;
            if ($code !== null && $code !== '' && $unit !== null && $unit !== '') {
                $englishUnitsByCode[$code] = $unit;
                $noLeading = ltrim($code, '0');
                if ($noLeading !== $code) {
                    $englishUnitsByCode[$noLeading] = $unit;
                }
            }
        }
        unset($enReader);

        $slUnitsReader = $this->csv($slUnitsPath);
        $slUnitTextById = [];
        foreach ($slUnitsReader->getRecords() as $row) {
            $id = isset($row['id']) ? trim((string) $row['id']) : null;
            if ($id === null || $id === '') {
                continue;
            }
            $unit = trim((string) ($row['unit'] ?? ''));
            $desc = trim((string) ($row['description'] ?? ''));
            $slUnitTextById[$id] = $desc !== '' ? $desc : $unit;
        }
        unset($slUnitsReader);

        $slListReader = $this->csv($slPath);
        $slUnitByTariffCode = [];
        foreach ($slListReader->getRecords() as $row) {
            $code = isset($row['code']) ? trim($row['code']) : null;
            $unitId = isset($row['unit_id']) ? trim((string) $row['unit_id']) : null;
            if ($code && $unitId && isset($slUnitTextById[$unitId])) {
                $slUnitByTariffCode[$code] = $slUnitTextById[$unitId];
            }
        }
        unset($slListReader, $slUnitTextById);

        $multiReader = $this->csv($multiPath);

        $codeNamesLookup = [];
        foreach ($multiReader->getRecords() as $row) {
            $raw = isset($row['CN_CODE']) ? trim((string) $row['CN_CODE']) : null;
            $code = $raw !== null ? $this->normalizeCode($raw) : null;
            if ($code === null || $code === '') {
                continue;
            }

            foreach ($this->locales as $locale) {
                $col = 'NAME_'.strtoupper($locale);
                if (array_key_exists($col, $row)) {
                    $val = trim((string) $row[$col]);
                    if ($val !== '') {
                        if (! isset($codeNamesLookup[$locale])) {
                            $codeNamesLookup[$locale] = [];
                        }
                        $codeNamesLookup[$locale][$code] = $val;
                    }
                }
            }
        }

        $multiReader = $this->csv($multiPath);

        $chunk = [];
        $chunkSize = 200;
        $codesInChunk = [];

        foreach ($multiReader->getRecords() as $row) {
            $raw = isset($row['CN_CODE']) ? trim((string) $row['CN_CODE']) : null;
            $code = $raw !== null ? $this->normalizeCode($raw) : null;
            if ($code === null || $code === '') {
                continue;
            }

            if (! isset($chunk[$code])) {
                $chunk[$code] = [
                    'year' => $year,
                    'code' => $code,
                    'name' => [],
                    'measure_unit' => [],
                ];
            }

            foreach ($this->locales as $locale) {
                $col = 'NAME_'.strtoupper($locale);
                if (array_key_exists($col, $row)) {
                    $val = trim((string) $row[$col]);
                    if ($val !== '') {
                        $chunk[$code]['name'][$locale] = $this->transformCnName($val, $code, $codeNamesLookup[$locale] ?? []);
                    }
                }
            }

            if (in_array('en', $this->locales, true)) {
                if (isset($englishUnitsByCode[$code])) {
                    $chunk[$code]['measure_unit']['en'] = $englishUnitsByCode[$code];
                } else {
                    $alt = ltrim($code, '0');
                    if ($alt !== '' && isset($englishUnitsByCode[$alt])) {
                        $chunk[$code]['measure_unit']['en'] = $englishUnitsByCode[$alt];
                    }
                }
            }
            if (in_array('sl', $this->locales, true) && isset($slUnitByTariffCode[$code])) {
                $chunk[$code]['measure_unit']['sl'] = $slUnitByTariffCode[$code];
            }

            if (! isset($codesInChunk[$code])) {
                $codesInChunk[$code] = true;
                if (count($codesInChunk) >= $chunkSize) {
                    $this->flushChunk($chunk);
                    $chunk = [];
                    $codesInChunk = [];
                }
            }
        }

        if (! empty($chunk)) {
            $this->flushChunk($chunk);
        }

        unset($multiReader, $englishUnitsByCode, $slUnitByTariffCode);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * Read a CSV file.
     */
    private function csv(string $absolutePath): Reader
    {
        $reader = Reader::createFromPath($absolutePath, 'r');
        $reader->setHeaderOffset(0);

        return $reader;
    }

    /**
     * Flush a chunk of data to the database.
     *
     * @param  array<string, mixed>  $chunk
     */
    private function flushChunk(array $chunk): void
    {
        if (empty($chunk)) {
            return;
        }

        $codes = array_keys($chunk);
        $existingRecords = DB::table('world_tariff_codes')
            ->where('year', $chunk[reset($codes)]['year'])
            ->whereIn('code', $codes)
            ->get(['code', 'name', 'measure_unit'])
            ->keyBy('code');

        $payload = [];
        foreach ($chunk as $rec) {
            $existingName = [];
            $existingUnits = [];

            if (isset($existingRecords[$rec['code']])) {
                $existing = $existingRecords[$rec['code']];
                if ($existing->name) {
                    $existingName = json_decode($existing->name, true) ?? [];
                }
                if ($existing->measure_unit) {
                    $existingUnits = json_decode($existing->measure_unit, true) ?? [];
                }
            }

            $names = array_merge($existingName, $rec['name']);
            $units = array_merge($existingUnits, $rec['measure_unit']);

            $payload[] = [
                'year' => $rec['year'],
                'code' => $rec['code'],
                'name' => json_encode($names, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'measure_unit' => empty($units)
                    ? null
                    : json_encode($units, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('world_tariff_codes')->upsert(
            $payload,
            ['year', 'code'],
            ['name', 'measure_unit', 'updated_at']
        );
    }

    /**
     * Ensure CSVs exist under storage/app/cn/{year}, streaming download to disk.
     *
     * @return array<string>|null
     */
    private function ensureYearCsvsAndReturnPaths(int $year): ?array
    {
        $disk = Storage::disk('local');
        $dir = "private/cn/{$year}";
        $disk->makeDirectory($dir);

        $filenames = [
            'cn_list_en.csv',
            'cn_list_multilingual.csv',
            'cn_list_sl.csv',
            'cn_list_sl_units.csv',
        ];

        $paths = [];

        foreach ($filenames as $file) {
            $relative = "{$dir}/{$file}";
            $absolute = $disk->path($relative);

            if (! $disk->exists($relative)) {
                $url = "https://www.datalinx.io/api/{$year}/{$file}";
                $tmp = $disk->path("{$relative}.tmp");
                @mkdir(dirname($tmp), 0777, true);

                $resp = Http::timeout(60)
                    ->connectTimeout(15)
                    ->withOptions(['sink' => $tmp])
                    ->get($url);

                if (! $resp->successful() || ! file_exists($tmp) || filesize($tmp) === 0) {
                    if (file_exists($tmp)) {
                        @unlink($tmp);
                    }

                    return null;
                }

                if ($disk->exists($relative)) {
                    $disk->delete($relative);
                }
                @rename($tmp, $absolute);
                if (! file_exists($absolute)) {
                    $disk->put($relative, file_get_contents($tmp));
                    @unlink($tmp);
                }
            }

            if (! file_exists($absolute)) {
                return null;
            }

            $paths[] = $absolute;
        }

        return $paths;
    }

    /**
     * Normalize CN code format coming from multilingual file.
     */
    private function normalizeCode(string $code): string
    {
        return str_replace(' ', '', $code);
    }

    /**
     * Transform CN name by removing leading dashes and building hierarchical name.
     */
    private function transformCnName(string $name, string $code, array $codeNamesLookup): string
    {
        $name = ltrim($name, '-');

        if (empty($name)) {
            return $code;
        }

        $hierarchicalParts = [];

        $hierarchicalParts[] = $this->capitalizeFirst($name);

        $currentCode = $code;
        while (strlen($currentCode) > 2) {
            $parentCode = substr($currentCode, 0, -2);
            if (strlen($parentCode) > 2) {
                $parentName = $codeNamesLookup[$parentCode] ?? null;
                if ($parentName) {
                    $cleanParentName = ltrim($parentName, '-');
                    if (! empty($cleanParentName)) {
                        $hierarchicalParts[] = $this->capitalizeFirst($cleanParentName);
                    }
                }
            }
            $currentCode = $parentCode;
        }

        $hierarchicalParts = array_reverse($hierarchicalParts);

        return implode(' > ', $hierarchicalParts);
    }

    /**
     * Capitalize only the first character (multibyte-safe) and lowercase the rest.
     */
    private function capitalizeFirst(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return $trimmed;
        }

        $firstChar = mb_substr($trimmed, 0, 1, 'UTF-8');
        $rest = mb_substr($trimmed, 1, null, 'UTF-8');

        return mb_strtoupper($firstChar, 'UTF-8').$rest;
    }
}
