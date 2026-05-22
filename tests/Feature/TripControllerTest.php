<?php

namespace Tests\Feature;

use App\Models\Route;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Ticket;
use App\Models\Train;
use App\Models\Trip;
use App\Models\User;
use App\Models\Wagon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => User::$role[3]]);
        $this->operatorUser = User::factory()->create(['role' => User::$role[0]]);
    }

    public function test_index_displays_trips_to_public()
    {
        Trip::factory()->count(3)->create(['depart_time' => now()->addDays(2)]);


        $response = $this->get(route('trips.index'));

        $response->assertStatus(200);
        $response->assertViewHas('trips');
    }

    public function test_user_can_view_trip_details()
    {
        $station = \App\Models\Station::factory()->create();
        $route = \App\Models\Route::factory()->create();

        $routeStop = new \App\Models\RouteStop([
            'order' => 1,
            'stop_time' => '00:15:00',
            'travel_time_to_next_station' => '02:00:00',
        ]);


        $routeStop->route_id = $route->id;
        $routeStop->station_id = $station->id;


        $routeStop->save();

        $train = \App\Models\Train::factory()->create();
        $wagon = \App\Models\Wagon::factory()->create(['train_id' => $train->id]);
        $seat = \App\Models\Seat::factory()->create(['wagon_id' => $wagon->id]);

        $trip = \App\Models\Trip::factory()->create([
            'route_id' => $route->id,
            'train_id' => $train->id
        ]);

        $response = $this->actingAs($this->user)->post(route('trips.details', $trip), [
            'trip_id' => $trip->id,
            'seat_ids' => [$seat->id],
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('trips.details');
        $response->assertViewHasAll(['seats', 'trip_id', 'availableStations']);
    }

    public function test_operator_can_create_trip()
    {
        $train = Train::factory()->create();
        $route = Route::factory()->create();
        $date = now()->addDays(5)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->operatorUser)->post(route('trips.tripCreate'), [
            'trains-0' => $train->id,
            'route-0'  => $route->id,
            'date'     => $date,
        ]);

        $trip = Trip::first();

        $response->assertRedirect(route('trips.show', $trip));
        $this->assertDatabaseHas('trips', [
            'train_id' => $train->id,
            'route_id' => $route->id,
        ]);
    }
}
