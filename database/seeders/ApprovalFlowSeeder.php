<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApprovalFlow;
use App\Models\ApprovalStep;
use App\Models\Role;

/**
 * Approval Flow Seed — Single Universal Flow
 * ───────────────────────────────────────────
 *
 * LEVEL HIERARCHY
 *   4 = Staff      (terendah — bisa submit)
 *   3 = Supervisor
 *   2 = Manager
 *   1 = Director   (tertinggi)
 *
 * FLOW: "Standard Flow"  (universal, satu untuk semua divisi)
 *   Step 1 – required_level 3  → Supervisor divisi yang sama (auto-skip jika tidak ada)
 *   Step 2 – required_level 2  → Manager divisi yang sama (auto-skip jika tidak ada)
 *   Step 3 – Approval Finance  → Staff finance yang bertugas approve (misal: Lina)
 *   Step 4 – Finance Manager   → Manager FAT/keuangan cross-divisi (misal: Wahyu)
 *   Step 5 – Director          → Persetujuan akhir
 *
 * AUTO-SKIP RULES (WorkflowService):
 *   • Step level-based di-skip jika submitter.level <= required_level
 *   • Step level-based di-skip jika tidak ada approver eligible di divisi
 *
 * FLOW: "Director Approval Flow"  (untuk request kategori Entertain)
 *   Step 1 – Approval Finance
 *   Step 2 – Finance Manager
 *   Step 3 – Director  (requires_director = true)
 */
class ApprovalFlowSeeder extends Seeder
{
    public function run(): void
    {
        ApprovalStep::query()->delete();
        ApprovalFlow::query()->delete();

        $director = Role::where('slug', 'director')->first();
        $appFinance = Role::where('slug', 'approval-finance')->first();
        $finManager = Role::where('slug', 'finance-manager')->first();

        // ── Standard Flow (universal, berlaku untuk semua divisi) ─────────
        $standard = ApprovalFlow::create([
            'name' => 'Standard Flow',
            'description' => 'Flow universal untuk semua divisi. '
                . 'Step level-based di-skip otomatis jika submitter '
                . 'sudah cukup level atau tidak ada approver yang eligible di divisinya.',
            'division_id' => null,
        ]);

        // Step 1: Supervisor atau lebih tinggi di divisi yang sama
        ApprovalStep::create([
            'approval_flow_id' => $standard->id,
            'step_order' => 1,
            'role_id' => null,
            'required_level' => 3,
        ]);

        // Step 2: Manager atau lebih tinggi di divisi yang sama
        ApprovalStep::create([
            'approval_flow_id' => $standard->id,
            'step_order' => 2,
            'role_id' => null,
            'required_level' => 2,
        ]);

        // Step 3: Approval Finance — staff finance yang bertugas approve (mis: Lina)
        ApprovalStep::create([
            'approval_flow_id' => $standard->id,
            'step_order' => 3,
            'role_id' => $appFinance?->id,
            'required_level' => null,
        ]);

        // Step 4: Finance Manager — Manager FAT / cross-divisi (mis: Wahyu), approval sebelum Direktur
        ApprovalStep::create([
            'approval_flow_id' => $standard->id,
            'step_order' => 4,
            'role_id' => $finManager?->id,
            'required_level' => null,
        ]);

        // Step 5: Director
        ApprovalStep::create([
            'approval_flow_id' => $standard->id,
            'step_order' => 5,
            'role_id' => $director?->id,
            'required_level' => null,
            'requires_director' => true,
        ]);

        // ── Director Approval Flow (untuk request kategori Entertain) ──────
        $dirFlow = ApprovalFlow::create([
            'name' => 'Director Approval Flow',
            'description' => 'Untuk pengeluaran kategori Entertain. Wajib disetujui Director.',
            'division_id' => null,
        ]);

        ApprovalStep::create([
            'approval_flow_id' => $dirFlow->id,
            'step_order' => 1,
            'role_id' => $appFinance?->id,
            'required_level' => null,
        ]);

        ApprovalStep::create([
            'approval_flow_id' => $dirFlow->id,
            'step_order' => 2,
            'role_id' => $finManager?->id,
            'required_level' => null,
        ]);

        ApprovalStep::create([
            'approval_flow_id' => $dirFlow->id,
            'step_order' => 3,
            'role_id' => $director?->id,
            'required_level' => null,
            'requires_director' => true,
        ]);
    }
}
