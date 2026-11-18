<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'code' => strtoupper(fake()->unique()->bothify('PRD-####')),
            'name' => fake()->words(3, true),
            'brand' => fake()->company(),
            'origin' => fake()->country(),
            'status' => 'in',
            'price_factory_cash' => 1000000,
            'price_baloochi_cash' => 1100000,
        ];
    }
}
