<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define all permissions with their human-readable descriptions
        $permissions = [
            [
                'name' => 'Kelola Pengaturan',
                'slug' => 'settings.manage',
                'description' => 'Akses halaman Kebijakan, Alur Persetujuan, dan Manajemen Permission',
                'roles' => ['admin'],
            ],
            [
                'name' => 'Lihat Laporan',
                'slug' => 'reports.view',
                'description' => 'Melihat dan mengekspor laporan pengajuan',
                'roles' => ['admin', 'director', 'manager', 'auditor'],
            ],
            [
                'name' => 'Proses Persetujuan',
                'slug' => 'approvals.process',
                'description' => 'Melihat daftar approval dan memproses approve/reject',
                'roles' => ['manager', 'director', 'finance', 'hse'],
            ],
            [
                'name' => 'Lihat Semua Pengajuan',
                'slug' => 'requests.view-all',
                'description' => 'Melihat semua pengajuan, tidak hanya milik sendiri',
                'roles' => ['admin', 'auditor'],
            ],
            [
                'name' => 'Hapus Pengajuan',
                'slug' => 'requests.delete',
                'description' => 'Menghapus pengajuan milik siapa saja, terlepas dari statusnya',
                'roles' => ['admin'],
            ],
        ];

        foreach ($permissions as $data) {
            $permission = Permission::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]
            );

            // Attach the default roles
            $roleIds = Role::whereIn('slug', $data['roles'])->pluck('id');
            $permission->roles()->sync($roleIds);
        }
    }
}
