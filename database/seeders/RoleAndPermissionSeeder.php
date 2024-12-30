<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super = Role::firstOrCreate(['name' => 'super_admin']);
        $panel = Role::firstOrCreate(['name' => 'panel_user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $penulisRole = Role::firstOrCreate(['name' => 'penulis']);
        
        // Memberikan permission yang lebih banyak ke Admin
        $adminPermissions = [
            // 1,
            // 2,
            // 3,
            // 4,
            // 5,
            // 6,
            7,
            8,
            9,
            10,
            11,
            12,
            25,
            26,
            27,
            28,
            29,
            30,
            31,
            32,
            33,
            34,
            35,
            36,
            37,
            38,
            39,
            40,
            41,
            42,
            43,
            44,
            45,
            46,
            47,
            48,
            55,
            56,
            57,
            58,
            59,
            60,
            61,
            62,
            63,
            64,
            65,
            66,
            67,
            68,
            69,
            70,
            71,
            72,
            73,
            74,
            75,
            76,
            77,
            78
        ];

        // Menyinkronkan permission yang diberikan ke Admin
        $adminRole->syncPermissions(Permission::whereIn('id', $adminPermissions)->get());

        // Memberikan permission terbatas ke Penulis
        $penulisPermissions = [
            // 1,
            // 2,
            7,
            8,
            25,
            26,
            27,
            28,
            29,
            30,
            31,
            32,
            33,
            34,
            35,
            36,
            55,
            56
        ];

        // Menyinkronkan permission yang diberikan ke Penulis
        $penulisRole->syncPermissions(Permission::whereIn('id', $penulisPermissions)->get());
    }
}
