<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPerDeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            ['name' => 'Admin IT', 'nik' => '12345IT', 'password' => '12345IT', 'departement_id' => '1', 'role_as' => '1'],
            ['name' => 'Admin TAX', 'nik' => '12345TAX', 'password' => '12345TAX', 'departement_id' => '2', 'role_as' => '1'],
            ['name' => 'Admin FINANCE', 'nik' => '12345FC', 'password' => '12345FC', 'departement_id' => '3', 'role_as' => '1'],
            ['name' => 'Admin MARKETING', 'nik' => '12345MKT', 'password' => '12345MKT', 'departement_id' => '4', 'role_as' => '1'],
            ['name' => 'Admin PLANNING & DESIGN', 'nik' => '12345PD', 'password' => '12345PD', 'departement_id' => '5', 'role_as' => '1'],
            ['name' => 'Admin ACCOUNTING', 'nik' => '12345ACC', 'password' => '12345ACC', 'departement_id' => '6', 'role_as' => '1'],
            ['name' => 'Admin LEGAL', 'nik' => '12345L', 'password' => '12345L', 'departement_id' => '7', 'role_as' => '1'],
            ['name' => 'Admin DOCUMENT CONTROL', 'nik' => '12345DC', 'password' => '12345DC', 'departement_id' => '8', 'role_as' => '1'],
            ['name' => 'Admin PROPERTY DEVELOPMENT', 'nik' => '12345PD', 'password' => '12345PD', 'departement_id' => '9', 'role_as' => '1'],
            ['name' => 'Admin FINANCE ACCOUNTING AND TAX', 'nik' => '12345FAT', 'password' => '12345FAT', 'departement_id' => '10', 'role_as' => '1'],
            ['name' => 'Admin PURCHASING', 'nik' => '12345P', 'password' => '12345P', 'departement_id' => '11', 'role_as' => '1'],
            // ['name' => 'Admin CORPORATE', 'nik' => '12345C', 'password' => '12345C', 'departement_id' => '12'],
            ['name' => 'Admin ESTATE', 'nik' => '12345E', 'password' => '12345E', 'departement_id' => '13', 'role_as' => '1'],
            ['name' => 'Admin QS', 'nik' => '12345QS', 'password' => '12345QS', 'departement_id' => '14', 'role_as' => '1'],
            ['name' => 'Admin SALES', 'nik' => '12345S', 'password' => '12345S', 'departement_id' => '15', 'role_as' => '1'],
            ['name' => 'Admin HRGA', 'nik' => '12345HRGA', 'password' => '12345HRGA', 'departement_id' => '16', 'role_as' => '1'],
            ['name' => 'Admin BUSINESS DEVELOPMENT', 'nik' => '12345BD', 'password' => '12345BD', 'departement_id' => '17', 'role_as' => '1'],
            ['name' => 'Admin CORPORATE', 'nik' => '12345CP', 'password' => '12345CP', 'departement_id' => '18', 'role_as' => '1'],
            ['name' => 'Admin TECHNIC & PROJECT', 'nik' => '12345TP', 'password' => '12345TP', 'departement_id' => '19', 'role_as' => '1'],
            // Tambahkan data lainnya...
        ];

        foreach ($data as $user) {
            DB::table('users')->insert([
                'nik' => $user['nik'],
                'name' => $user['name'],
                'departement_id' => $user['departement_id'],
                'password' => Hash::make($user['password']),
                'created_at' => $now,
                'updated_at' => $now,
                'role_as' => $user['role_as'],
            ]);
        }
    }
}
