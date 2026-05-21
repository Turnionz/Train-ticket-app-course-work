<?php

namespace Tests\Feature;

use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operatorUser = User::factory()->create(['role' => 'operator']);
        $this->standardUser = User::factory()->create(['role' => 'user']);
    }

    public function test_index_displays_stations()
    {
        Station::factory()->count(3)->create();

        $response = $this->actingAs($this->standardUser)->get(route('stations.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stations');
    }

    public function test_operator_can_create_station()
    {
        $response = $this->actingAs($this->operatorUser)->post(route('stations.store'), [
            'address'  => 'Kyiv Central',
            'capacity' => 500,
        ]);

        $response->assertRedirect(route('stations.index'));
        $this->assertDatabaseHas('stations', [
            'address' => 'Kyiv Central'
        ]);
    }

    public function test_non_operator_cannot_create_station()
    {
        $response = $this->actingAs($this->standardUser)->post(route('stations.store'), [
            'address'  => 'Lviv Central',
            'capacity' => 300,
        ]);

        $response->assertForbidden();
    }

    public function test_operator_can_delete_station()
    {
        $station = Station::factory()->create();

        $response = $this->actingAs($this->operatorUser)->delete(route('stations.destroy', $station));

        $response->assertRedirect(route('stations.index'));
        $this->assertDatabaseMissing('stations', ['id' => $station->id]);
    }
}
