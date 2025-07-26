<?php

use Eclipse\World\Models\Country;
use Eclipse\World\Models\Region;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Create EU special region
        $euRegion = Region::create([
            'code' => 'EU',
            'name' => 'European Union',
            'is_special' => true,
        ]);

        $euMemberCountries = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
        ];

        $existingCountries = Country::whereIn('id', $euMemberCountries)->pluck('id');
        $membershipData = $existingCountries->mapWithKeys(fn ($countryId) => [
            $countryId => [
                'start_date' => Carbon::now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $euRegion->specialCountries()->attach($membershipData);
    }

    public function down(): void
    {
        Region::where('code', 'EU')->delete();
    }
};
