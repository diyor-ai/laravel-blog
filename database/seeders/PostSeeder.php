<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::create([
            'user_id' => 1,
            'title' => 'Abdulazimov Mukhammaddiyor',
            'short_content' => 'qiqsa maqola yani short_content',
            'content' => 'misol uchun content',
            'photo' => null,
        ]);

        Post::create([
            'user_id' => 1,
            'title' => 'Abdulazimov Mukhammaddiyor',
            'short_content' => 'qiqsa maqola yani short_content',
            'content' => 'misol uchun content',
            'photo' => null,
        ]);

        Post::create([
            'user_id' => 1,
            'title' => 'Abdulazimov Mukhammaddiyor',
            'short_content' => 'qiqsa maqola yani short_content',
            'content' => 'misol uchun content',
            'photo' => null,
        ]);
    }
}
