<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\Train;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'train_id' => Train::factory(),
            'route_id' => Route::factory(),
            'depart_time' => now()->addDays(1),
            'arrival_time' => now()->addDays(1)->addHours(4),
        ];
    }
}
