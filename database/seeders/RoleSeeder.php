<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Employee',
            'Marketing',
            'HSE',
            'Finance',
            'Approval Finance',   // Finance staff yang jadi approver (step 3)
            'Finance Manager',    // Manager FAT yg approve sebelum Direktur (step 4)
            'Manager',
            'Director',
            'Admin',
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'slug' => Str::slug($role),
            ]);
        }
    }
}
