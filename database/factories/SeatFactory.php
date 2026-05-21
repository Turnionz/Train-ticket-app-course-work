<?php

namespace Database\Factories;

use App\Models\Seat;
use App\Models\Wagon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seat>
 */
class SeatFactory extends Factory
{
    protected $model = Seat::class;

    public function definition(): array
    {
        return [
            'wagon_id' => Wagon::factory(),
            'seat_number' => $this->faker->unique()->numberBetween(1, 60),
            'class' => '2-й клас',
        ];
    }
}
