<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Request as BudgetRequest;
use App\Models\Approval;
use App\Models\ApprovalFlow;
use App\Models\ApprovalStep;
use App\Models\ClientCode;

class RBACTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\ApprovalFlowSeeder::class);
    }

    /** Helper: create a user with given role slug */
    private function userWithRole(string $roleSlug): User
    {
        $user = User::factory()->create();
        $role = Role::where('slug', $roleSlug)->first();
        $user->roles()->attach($role->id);
        return $user;
    }

    // ─── Settings: permission:settings.manage ──────────────────────────────

    public function test_employee_cannot_access_settings()
    {
        $response = $this->actingAs($this->userWithRole('employee'))
            ->get(route('settings.flows'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_settings()
    {
        $response = $this->actingAs($this->userWithRole('admin'))
            ->get(route('settings.flows'));
        $response->assertStatus(200);
    }

    // ─── Reports: permission:reports.view ──────────────────────────────────

    public function test_employee_cannot_access_reports()
    {
        $response = $this->actingAs($this->userWithRole('employee'))
            ->get(route('reports.index'));
        $response->assertStatus(403);
    }

    public function test_manager_can_access_reports()
    {
        $response = $this->actingAs($this->userWithRole('manager'))
            ->get(route('reports.index'));
        // 200 or redirect (login not needed since we are authed)
        $response->assertStatus(200);
    }

    // ─── Approvals: controller-level role check ────────────────────────────

    public function test_wrong_role_cannot_update_approval()
    {
        $employee = $this->userWithRole('employee');
        $client = ClientCode::create(['prefix' => 'GOV1', 'instansi_singkat' => 'BPS', 'counter' => 1, 'name' => 'BPS']);
        $owner = User::factory()->create();

        $request = BudgetRequest::create([
            'user_id' => $owner->id,
            'client_code_id' => $client->id,
            'type' => 'budget',
            'title' => 'Test',
            'status' => 'submitted',
            'total_amount' => 1000,
        ]);

        $managerRole = Role::where('slug', 'manager')->first();
        $flow = ApprovalFlow::create(['name' => 'Flow', 'is_active' => true]);
        $step = ApprovalStep::create(['approval_flow_id' => $flow->id, 'role_id' => $managerRole->id, 'step_order' => 1]);
        $approval = Approval::create(['request_id' => $request->id, 'approval_step_id' => $step->id, 'status' => 'pending']);

        $response = $this->actingAs($employee)->put(route('approvals.update', $approval), ['status' => 'approved']);
        $response->assertStatus(403);
    }

    public function test_correct_role_can_update_approval()
    {
        $manager = $this->userWithRole('manager');
        $client = ClientCode::create(['prefix' => 'GOV2', 'instansi_singkat' => 'BPS', 'counter' => 1, 'name' => 'BPS2']);
        $owner = User::factory()->create();

        $request = BudgetRequest::create([
            'user_id' => $owner->id,
            'client_code_id' => $client->id,
            'type' => 'budget',
            'title' => 'Test',
            'status' => 'submitted',
            'total_amount' => 1000,
        ]);

        $managerRole = Role::where('slug', 'manager')->first();
        $flow = ApprovalFlow::create(['name' => 'Flow2', 'is_active' => true]);
        $step = ApprovalStep::create(['approval_flow_id' => $flow->id, 'role_id' => $managerRole->id, 'step_order' => 1]);
        $approval = Approval::create(['request_id' => $request->id, 'approval_step_id' => $step->id, 'status' => 'pending']);

        $response = $this->actingAs($manager)->put(route('approvals.update', $approval), ['status' => 'approved']);
        $response->assertRedirect(route('approvals.index'));
        $this->assertDatabaseHas('approvals', ['id' => $approval->id, 'status' => 'approved']);
    }
}
