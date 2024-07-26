<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Web Design']);
        Category::create(['name' => 'Web Development']);
        Category::create(['name' => 'Online Marketingn']);
        Category::create(['name' => 'Keyword Research']);
        Category::create(['name' => 'Email Marketing']);
    }
}
