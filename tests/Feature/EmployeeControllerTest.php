<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operatorUser = User::factory()->create(['role' => User::$role[1]]);
        $this->standardUser = User::factory()->create(['role' => User::$role[3]]);
    }

    public function test_index_displays_employees()
    {
        // Створюємо працівника та одразу прив'язуємо до нього користувача
        $employee = Employee::factory()->create();
        $employee->user()->save(User::factory()->make(['first_name' => 'John']));

        $response = $this->actingAs($this->operatorUser)->get(route('employees.index'));

        $response->assertStatus(200);
        $response->assertViewHas('employees');
    }

    public function test_operator_can_create_employee_and_associated_user()
    {
        $validRole = User::$role[1];

        $response = $this->actingAs($this->operatorUser)->post(route('employees.store'), [
            'email'      => 'driver@example.com',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'role'       => $validRole,
            'type'       => 'Механік',
            'password'   => 'password123',
        ]);

        $employee = Employee::first();

        $response->assertRedirect(route('employees.show', $employee));

        $this->assertDatabaseHas('employees', [
            'employee_type' => 'Механік'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'driver@example.com',
            'first_name' => 'John',
        ]);
    }

    public function test_non_operator_cannot_create_employee()
    {
        $response = $this->actingAs($this->standardUser)->post(route('employees.store'), [
            'email' => 'driver@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'role' => 'employee',
            'password' => 'password',
        ]);

        $response->assertForbidden();
    }

    public function test_operator_can_delete_employee()
    {
        $employee = Employee::factory()->create();
        $user = $employee->user()->save(User::factory()->make());

        $response = $this->actingAs($this->operatorUser)->delete(route('employees.destroy', $employee));

        $response->assertRedirect(route('employees.index'));
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
