<?php

namespace Database\Factories;

use App\Models\Train;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Train>
 */
class TrainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'train_number' => fake()->randomNumber(3, false),
            'type' => fake()->randomElement(Train::$type),
        ];
    }
}
