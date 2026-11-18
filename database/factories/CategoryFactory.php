<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Category> */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'icon' => 'fa-tooth',
            'type' => 'equipment',
            'is_digital' => false,
            'sort_order' => 0,
        ];
    }
}
