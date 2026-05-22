<?php

namespace Tests\Feature;

use App\Models\Seat;
use App\Models\Ticket;
use App\Models\Train;
use App\Models\User;
use App\Models\Wagon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operatorUser = User::factory()->create(['role' => User::$role[1]]);
    }

    public function test_operator_can_create_train()
    {
        $response = $this->actingAs($this->operatorUser)->post(route('trains.store'), [
            'train_number' => '12345',
            'type' => Train::$type[0]
        ]);

        $train = Train::where('train_number', '12345')->first();

        $response->assertRedirect(route('trains.show', $train));
        $this->assertDatabaseHas('trains', [
            'train_number' => 12345,
            'type' => Train::$type[0]
        ]);
    }

    public function test_operator_cannot_delete_train_with_active_tickets()
    {
        $train = Train::factory()->create();
        $wagon = Wagon::factory()->create(['train_id' => $train->id]);
        $seat = Seat::factory()->create(['wagon_id' => $wagon->id]);

        Ticket::factory()->create([
            'seat_id' => $seat->id,
            'status' => Ticket::$status[0]
        ]);

        $response = $this->actingAs($this->operatorUser)->delete(route('trains.destroy', $train));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('trains', ['id' => $train->id]);
    }

    public function test_operator_can_delete_train_without_active_tickets()
    {
        $train = Train::factory()->create();
        $wagon = Wagon::factory()->create(['train_id' => $train->id]);

        $response = $this->actingAs($this->operatorUser)->delete(route('trains.destroy', $train));

        $response->assertRedirect(route('trains.index'));
        $this->assertDatabaseMissing('trains', ['id' => $train->id]);


        $this->assertDatabaseHas('wagons', [
            'id' => $wagon->id,
            'train_id' => null
        ]);
    }
}
