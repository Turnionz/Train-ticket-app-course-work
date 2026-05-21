<?php

namespace Database\Factories;

use App\Models\Seat;
use App\Models\Station;
use App\Models\Ticket;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'seat_id' => Seat::factory(),
            'trip_id' => Trip::factory(),

            'status' => Ticket::$status[0],

            'departing_station' => Station::factory(),
            'arriving_station' => Station::factory(),
        ];
    }
}
