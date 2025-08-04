<?php

namespace Eclipse\World\Factories;

use Eclipse\World\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->lexify('???'),
            'name' => fake()->currencyCode(),
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }
}
