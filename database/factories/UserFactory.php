<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'age' => fake()->numberBetween(18, 90),
            'version' => 1,
            'reputation_score' => 0,
        ];
    }
}
