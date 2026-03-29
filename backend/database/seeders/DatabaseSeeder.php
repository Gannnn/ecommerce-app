<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        Admin::create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->call([
            ProductCategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
