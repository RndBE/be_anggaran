<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Policy;
use App\Models\TravelZone;
use App\Models\ClientCode;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        Policy::insert([
            ['name' => 'Max Makan Customer', 'key' => 'MEAL_CUSTOMER_MAX', 'value' => 250000, 'description' => 'Makan customer max per orang'],
            ['name' => 'Max Hotel Weekday', 'key' => 'HOTEL_WEEKDAY_MAX', 'value' => 400000, 'description' => 'Hotel max weekday'],
            ['name' => 'Max Hotel Weekend', 'key' => 'HOTEL_WEEKEND_MAX', 'value' => 500000, 'description' => 'Hotel max weekend'],
            ['name' => 'Lembur Libur', 'key' => 'LEMBUR_PER_HARI', 'value' => 250000, 'description' => 'Tunjangan hari libur'],
        ]);

        TravelZone::query()->delete(); // Bersihkan data lama jika ada
        TravelZone::insert([
            ['zone' => 1, 'name' => 'Zona 1 (≤ 60 km): Yogyakarta, Magelang, Klaten', 'meal_allowance' => 0],
            ['zone' => 2, 'name' => 'Zona 2 (60–150 km): Semarang, Surakarta/Solo, Kebumen, Wonosobo, Boyolali, Purworejo', 'meal_allowance' => 50000],
            ['zone' => 3, 'name' => 'Zona 3 (> 150 km P. Jawa): Jakarta, Bandung, Surabaya, Malang, Cirebon', 'meal_allowance' => 100000],
            ['zone' => 4, 'name' => 'Zona 4 (Luar Jawa): Denpasar, Medan, Balikpapan, Makassar, dll', 'meal_allowance' => 150000],
        ]);

        ClientCode::insert([
            ['prefix' => 'GOV1', 'instansi_singkat' => 'BBWS', 'name' => 'Balai Besar Wilayah Sungai'],
            ['prefix' => 'GOV2', 'instansi_singkat' => 'BRANTAS', 'name' => 'Brantas Abipraya'],
            ['prefix' => 'POE', 'instansi_singkat' => 'MINARTA', 'name' => 'Minarta Dutahutama'],
        ]);
    }
}
