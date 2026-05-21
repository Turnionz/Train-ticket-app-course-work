<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        return [
            'depart_station' => Station::factory(),
            'arrival_station' => Station::factory(),
        ];
    }
}
