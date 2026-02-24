<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use Illuminate\Support\Facades\Hash;

/**
 * Creates one demo user per role.
 * Also creates realistic demo users per division, at all levels.
 *
 * Credentials: {slug}@demo.com / password
 * Examples:
 *   employee@demo.com  / password   → Level 4 (staff, terendah)
 *   manager@demo.com   / password   → Level 2 (manager, IT division)
 *   director@demo.com  / password   → Level 1 (director, tertinggi)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all()->keyBy('slug');
        $divisions = Division::all()->keyBy('slug');

        // ── 1. One demo user per role (simple) ───────────────────────────
        foreach ($roles as $role) {
            $user = User::firstOrCreate(
                ['email' => "{$role->slug}@demo.com"],
                [
                    'name' => "Demo {$role->name}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'level' => $this->levelForRole($role->slug),
                    'division_id' => $this->divisionForRole($role->slug, $divisions),
                ]
            );
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // ── 2. Realistic per-division users at each level ─────────────────
        $employeeRole = $roles['employee'] ?? null;
        $managerRole = $roles['manager'] ?? null;
        $divisionUsers = [
            // [division_slug, level, name_suffix, email]
            ['it', 4, 'Staff IT', 'it.staff@demo.com'],
            ['it', 3, 'Supervisor IT', 'it.supervisor@demo.com'],
            ['it', 2, 'Manager IT', 'it.manager@demo.com'],
            ['hr', 4, 'Staff HR', 'hr.staff@demo.com'],
            ['hr', 3, 'Supervisor HR', 'hr.supervisor@demo.com'],
            ['hr', 2, 'Manager HR', 'hr.manager@demo.com'],
            ['marketing', 4, 'Staff Marketing', 'mkt.staff@demo.com'],
            ['marketing', 2, 'Manager Marketing', 'mkt.manager@demo.com'],
            ['sales', 4, 'Staff Sales', 'sales.staff@demo.com'],
            ['sales', 2, 'Manager Sales', 'sales.manager@demo.com'],
            ['finance', 4, 'Staff Finance', 'fin.staff@demo.com'],
            ['finance', 2, 'Manager Finance', 'fin.manager@demo.com'],
            ['operations', 4, 'Staff Ops', 'ops.staff@demo.com'],
            ['operations', 2, 'Manager Ops', 'ops.manager@demo.com'],
        ];

        foreach ($divisionUsers as [$divSlug, $level, $name, $email]) {
            $division = $divisions[$divSlug] ?? null;
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'level' => $level,
                    'division_id' => $division?->id,
                ]
            );

            // Assign role based on level
            $role = $level >= 3 ? ($managerRole ?? $employeeRole) : ($employeeRole ?? null);
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /** Map role slug to a sensible default level */
    private function levelForRole(string $slug): int
    {
        return match ($slug) {
            'director', 'admin' => 1,
            'manager', 'hse', 'auditor' => 2,
            'finance', 'marketing', 'sales' => 3,
            default => 4,  // employee = staff (terendah)
        };
    }

    /** Assign a representative division to the per-role demo users */
    private function divisionForRole(string $slug, $divisions): ?int
    {
        $map = [
            'finance' => 'finance',
            'marketing' => 'marketing',
            'sales' => 'sales',
            'hse' => 'operations',
            'manager' => 'it',
            'auditor' => 'finance',
        ];

        $divSlug = $map[$slug] ?? null;
        return $divSlug ? ($divisions[$divSlug]?->id ?? null) : null;
    }
}
