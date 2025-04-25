<?php

namespace Eclipse\World\Factories;

use Eclipse\World\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->country,
            'code' => $this->faker->unique()->countryCode,
            'is_special' => false,
            'parent_id' => null,
        ];
    }
}
