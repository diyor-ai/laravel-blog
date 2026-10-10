<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Demo account for local development only.
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);
    }
}
