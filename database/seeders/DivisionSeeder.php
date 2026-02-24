<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use Illuminate\Support\Str;

/**
 * Divisi nyata perusahaan.
 * Gunakan firstOrCreate agar tidak duplikat saat re-seed sebagian.
 */
class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            'Software',
            'Marketing BD',
            'FAT & Supply Chain',
        ];

        foreach ($divisions as $name) {
            Division::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }
    }
}
