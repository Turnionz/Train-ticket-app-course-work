<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\Employee;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operatorUser = User::factory()->create(['role' => 'operator']);
        $this->employeeUser = User::factory()->create(['role' => 'employee']);
    }

    public function test_employee_can_view_crews_index()
    {
        Crew::factory()->count(2)->create();

        $response = $this->actingAs($this->employeeUser)->get(route('crews.index'));

        $response->assertStatus(200);
        $response->assertViewHas('crews');
    }

    public function test_operator_can_create_crew_with_assignments()
    {
        $employee = Employee::factory()->create(['crew_id' => null]);
        $trip = Trip::factory()->create(['depart_time' => now()->addDays(1)]);


        $response = $this->actingAs($this->operatorUser)->post(route('crews.store'), [
            'employees-0' => $employee->id,
            'trips-0'     => $trip->id,
        ]);

        $crew = Crew::first();

        $response->assertRedirect(route('crews.show', $crew));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'crew_id' => $crew->id,
        ]);
        $this->assertDatabaseHas('assignments', [
            'crew_id' => $crew->id,
            'trip_id' => $trip->id,
        ]);
    }

    public function test_operator_can_delete_crew_and_nullify_employees()
    {
        $crew = Crew::factory()->create();
        $employee = Employee::factory()->create(['crew_id' => $crew->id]);

        $response = $this->actingAs($this->operatorUser)->delete(route('crews.destroy', $crew));

        $response->assertRedirect(route('crews.index'));
        $this->assertDatabaseMissing('crews', ['id' => $crew->id]);
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'crew_id' => null,
        ]);
    }
}
