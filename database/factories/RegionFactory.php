<?php

namespace Eclipse\World\Factories;

use Eclipse\World\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Region::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'name' => $this->faker->unique()->words(2, true),
            'is_special' => false,
        ];
    }

    /**
     * Set the region as special.
     *
     * @return $this
     */
    public function special(): static
    {
        return $this->state(['is_special' => true]);
    }

    /**
     * Set the region as geographical.
     *
     * @return $this
     */
    public function geographical(): static
    {
        return $this->state(['is_special' => false]);
    }

    /**
     * Set the region as a child of a parent region.
     *
     * @return $this
     */
    public function withParent(Region $parent): static
    {
        return $this->state(['parent_id' => $parent->id]);
    }
}
