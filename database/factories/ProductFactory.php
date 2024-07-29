<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'name' => fake()->name(),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 0, 1000),
            'discount' => fake()->randomFloat(2, 0, 1000),
            'image' => fake()->imageUrl(640, 480, 'animals', true),
            'stock' => fake()->numberBetween(0, 100),
            'score' => fake()->numberBetween(0, 100),
            'active' => fake()->boolean(),
            'is_hot' => fake()->boolean(),
        ];
    }
}
