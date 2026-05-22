<?php

namespace Tests\Feature;

use App\Models\Seat;
use App\Models\Ticket;
use App\Models\Train;
use App\Models\User;
use App\Models\Wagon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WagonControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operatorUser = User::factory()->create(['role' => 'operator']);
    }

    public function test_operator_can_store_wagon_without_train()
    {
        $validPresetType = array_key_first(\App\Models\Wagon::getPresets()) ?? 'Standard';

        $response = $this->actingAs($this->operatorUser)->post(route('wagons.store'), [
            'amount' => 1,
            'type_select' => $validPresetType,
            'class_select' => Wagon::$type[2],
            'train_number' => null
        ]);

        $response->assertRedirect(route('wagons.index'));
        $this->assertDatabaseCount('wagons', 1);
    }

    public function test_operator_can_delete_wagon_without_active_tickets()
    {
        $wagon = Wagon::factory()->create();

        $response = $this->actingAs($this->operatorUser)->delete(route('wagons.destroy', $wagon));

        $response->assertRedirect(route('wagons.index'));
        $this->assertDatabaseMissing('wagons', ['id' => $wagon->id]);
    }

    public function test_operator_cannot_delete_wagon_with_active_tickets()
    {
        $wagon = Wagon::factory()->create();
        $seat = Seat::factory()->create(['wagon_id' => $wagon->id]);


        Ticket::factory()->create([
            'seat_id' => $seat->id,
            'status' => 'booked'
        ]);

        $response = $this->actingAs($this->operatorUser)->delete(route('wagons.destroy', $wagon));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('wagons', ['id' => $wagon->id]);
    }
}
