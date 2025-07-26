<?php

namespace Eclipse\World\Factories;

use Eclipse\World\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->lexify('??'),
            'a3_id' => fake()->lexify('???'),
            'num_code' => str_pad(fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'name' => fake()->country(),
            'flag' => fake()->emoji(),
            'region_id' => null,
        ];
    }
}
