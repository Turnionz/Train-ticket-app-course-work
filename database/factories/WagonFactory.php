<?php

namespace Database\Factories;

use App\Models\Train;
use App\Models\Wagon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wagon>
 */
class WagonFactory extends Factory
{
    protected $model = Wagon::class;

    public function definition(): array
    {
        return [
            'train_id' => Train::factory(),
            'wagon_number' => $this->faker->numberBetween(1, 10),
            'type' => 'Сидячий',
            'layout_map' => [],
        ];
    }
}
