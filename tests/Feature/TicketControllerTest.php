<?php

namespace Tests\Feature;

use App\Models\Seat;
use App\Models\Station;
use App\Models\Ticket;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'user']);
    }

    public function test_user_can_view_their_tickets()
    {
        Ticket::factory()->create(['user_id' => $this->user->id]);

        Ticket::factory()->create();

        $response = $this->actingAs($this->user)->get(route('tickets.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tickets');

        $this->assertCount(1, $response->original->getData()['tickets']);
    }

    public function test_user_can_buy_ticket()
    {
        $user = User::factory()->create(['role' => User::$role[3]]);
        $trip = Trip::factory()->create();
        $seat = Seat::factory()->create();
        $stationA = Station::factory()->create();
        $stationB = Station::factory()->create();

        $response = $this->actingAs($user)->post(route('trips.buy'), [
            'trip_id'  => $trip->id,
            'seat_ids' => [$seat->id],

            'depart'   => ['seat_' . $seat->id => $stationA->id],
            'arrive'   => ['seat_' . $seat->id => $stationB->id],
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('trips.payment');
        $response->assertViewHas('tickets');

        $this->assertDatabaseHas('tickets', [
            'user_id'           => $user->id,
            'trip_id'           => $trip->id,
            'seat_id'           => $seat->id,
            'departing_station' => $stationA->id,
            'arriving_station'  => $stationB->id,
            'status'            => Ticket::$status[0],
        ]);
    }

    public function test_owner_can_cancel_ticket()
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('tickets.destroy', $ticket));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_user_cannot_cancel_others_ticket()
    {
        $otherUser = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete(route('tickets.destroy', $ticket));

        $response->assertForbidden();
    }
}
