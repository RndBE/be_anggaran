<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\ClientCode;

class ReimbursementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles & Workflows
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\ApprovalFlowSeeder::class);

        // Fetch Employee Role
        $employeeRole = Role::where('slug', 'employee')->first();

        // Setup User
        $this->user = User::factory()->create();
        $this->user->roles()->attach($employeeRole->id);

        // Setup Client Code
        $this->clientCode = ClientCode::create([
            'prefix' => 'GOV1',
            'instansi_singkat' => 'BPS',
            'counter' => 1,
            'name' => 'Badan Pusat Statistik'
        ]);

        // Policies
        \App\Models\Policy::create(['name' => 'Max Hotel Limit', 'key' => 'max_hotel_limit', 'value' => 500000]);
        \App\Models\Policy::create(['name' => 'Max Meal Customer Limit', 'key' => 'max_meal_customer_limit', 'value' => 250000]);

    }

    public function test_can_view_request_creation_page()
    {
        $response = $this->actingAs($this->user)->get('/requests/create');
        $response->assertStatus(200);
    }

    public function test_can_submit_valid_reimbursement()
    {

        $response = $this->actingAs($this->user)->post('/requests', [
            'type' => 'reimbursement',
            'title' => 'Test Trip to Jakarta',
            'client_code_id' => $this->clientCode->id,
            'description' => 'Meeting with BPS',
            'items' => [
                [
                    'type' => 'hotel',
                    'amount' => 450000,
                    'description' => 'Hotel Ibis'
                ]
            ]
        ]);

        $response->assertRedirect('/requests');
        $this->assertDatabaseHas('requests', [
            'user_id' => $this->user->id,
            'title' => 'Test Trip to Jakarta',
            'total_amount' => 450000
        ]);
    }

    public function test_rejects_public_transport_for_reimbursement()
    {
        $response = $this->actingAs($this->user)->post('/requests', [
            'type' => 'reimbursement',
            'title' => 'Test Trip',
            'client_code_id' => $this->clientCode->id,
            'items' => [
                [
                    'type' => 'transport',
                    'amount' => 50000,
                    'description' => 'Kereta Api' // Will fail validation in service (mocked or actual)
                ]
            ]
        ]);

        // Actually the service just returns false, the controller throws Exception => back with error
        $response->assertSessionHasErrors(['error']);
    }

    public function test_rejects_meal_customer_above_limit()
    {
        $response = $this->actingAs($this->user)->post('/requests', [
            'type' => 'reimbursement',
            'title' => 'Test Meal',
            'client_code_id' => $this->clientCode->id,
            'items' => [
                [
                    'type' => 'meal_customer',
                    'amount' => 300000, // Policy is 250000
                    'description' => 'Lunch with client'
                ]
            ]
        ]);

        $response->assertSessionHasErrors(['error']);
    }
}
