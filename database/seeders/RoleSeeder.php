<?php

namespace Database\Seeders;

use App\Models\Accounts\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['buyer', 'seller', 'logistics', 'rider', 'admin'] as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }
}
