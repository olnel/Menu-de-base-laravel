<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {

        $users = [
            [
                'name' => 'ADMIN',
                'email' => 'admin@admin.mg',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
                'is_dna' => true,
            ],
                    ];
        User::insert($users);
    }
}
