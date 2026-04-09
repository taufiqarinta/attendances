<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPWSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userNik = 'SA-925001';
        $newPassword = 'superadmin.tanrise'; // atau password baru sesuai kebutuhan

        DB::table('users')->where('nik', $userNik)->update([
            'password' => Hash::make($newPassword),
            'updated_at' => now(),
        ]);
    }
}
