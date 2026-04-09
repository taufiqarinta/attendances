<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            ['name' => 'IT'],
            ['name' => 'TAX'],
            ['name' => 'FINANCE'],
            ['name' => 'MARKETING'],
            ['name' => 'PLANNING & DESIGN'],
            ['name' => 'ACCOUNTING'],
            ['name' => 'LEGAL'],
            ['name' => 'DOCUMENT CONTROL'],
            ['name' => 'PROPERTY DEVELOPMENT'],
            ['name' => 'FINANCE, ACCOUNTING AND TAX'],
            ['name' => 'PURCHASING'],
            ['name' => 'CORPORATE'],
            ['name' => 'ESTATE'],
            ['name' => 'QS'],
            ['name' => 'SALES'],
            ['name' => 'HRGA'],
            ['name' => 'BUSINESS DEVELOPMENT'],
            ['name' => 'CORPORATE'],
            ['name' => 'TECHNIC & PROJECT'],
        ];

        foreach ($data as $departement) {
            DB::table('departements')->insert([
                'name' => $departement['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
