<?php

namespace Eclipse\World\Factories;

use Eclipse\World\Models\TariffCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class TariffCodeFactory extends Factory
{
    protected $model = TariffCode::class;

    public function definition(): array
    {
        return [
            'year' => (int) date('Y'),
            'code' => $this->faker->unique()->numerify('####'),
            'name' => [
                'en' => $this->faker->words(3, true),
            ],
            'measure_unit' => [
                'en' => $this->faker->randomElement(['pcs', 'kg', 'l', 'm']),
            ],
        ];
    }
}
