<?php

namespace Database\Seeders;

use App\Models\ApprovalFlow;
use Illuminate\Database\Seeder;

class LhpApprovalFlowSeeder extends Seeder
{
    /**
     * Seed the default LHP approval flow.
     *
     * Chain: Supervisor (Lv≤3, Div) → Manager (Lv≤2, Div) → HSE → Finance
     * This mirrors the original hardcoded chain:
     *   level3 → level2 → k3 → hrd → finance
     */
    public function run(): void
    {
        // Remove existing LHP flows to avoid duplicates
        ApprovalFlow::where('flow_type', 'lhp')->delete();

        $flow = ApprovalFlow::create([
            'name' => 'Alur Persetujuan LHP',
            'description' => 'Alur default untuk Laporan Hasil Perjalanan Dinas',
            'flow_type' => 'lhp',
            'is_active' => true,
        ]);

        // Step 1: Supervisor (Level ≤ 3) dari divisi yang sama
        $flow->steps()->create([
            'role_id' => null,
            'required_level' => 3,
            'step_order' => 1,
            'requires_director' => false,
        ]);

        // Step 2: Manager (Level ≤ 2) dari divisi yang sama
        $flow->steps()->create([
            'role_id' => null,
            'required_level' => 2,
            'step_order' => 2,
            'requires_director' => false,
        ]);

        // Step 3: HSE (K3)
        $hseRole = \App\Models\Role::where('slug', 'hse')->first();
        if ($hseRole) {
            $flow->steps()->create([
                'role_id' => $hseRole->id,
                'required_level' => null,
                'step_order' => 3,
                'requires_director' => false,
            ]);
        }

        // Step 4: HRD
        $hrdRole = \App\Models\Role::where('slug', 'hrd')->first();
        if ($hrdRole) {
            $flow->steps()->create([
                'role_id' => $hrdRole->id,
                'required_level' => null,
                'step_order' => 4,
                'requires_director' => false,
            ]);
        }

        // Step 5: Finance
        $financeRole = \App\Models\Role::where('slug', 'finance')->first();
        if ($financeRole) {
            $flow->steps()->create([
                'role_id' => $financeRole->id,
                'required_level' => null,
                'step_order' => 5,
                'requires_director' => false,
            ]);
        }
    }
}
