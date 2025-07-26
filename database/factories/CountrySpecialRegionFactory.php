<?php

namespace Eclipse\World\Factories;

use Eclipse\World\Models\Country;
use Eclipse\World\Models\CountrySpecialRegion;
use Eclipse\World\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountrySpecialRegionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CountrySpecialRegion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'region_id' => Region::factory()->special(),
            'start_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'end_date' => $this->faker->optional(0.3)->dateTimeBetween('now', '+2 years')?->format('Y-m-d'),
        ];
    }

    /**
     * Set the region as active.
     *
     * @return $this
     */
    public function active(): static
    {
        return $this->state([
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'end_date' => null,
        ]);
    }

    /**
     * Set the region as ended.
     *
     * @return $this
     */
    public function ended(): static
    {
        return $this->state([
            'start_date' => $this->faker->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * Set the region as future.
     *
     * @return $this
     */
    public function future(): static
    {
        return $this->state([
            'start_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'end_date' => $this->faker->optional(0.5)->dateTimeBetween('+6 months', '+2 years')?->format('Y-m-d'),
        ]);
    }
}
