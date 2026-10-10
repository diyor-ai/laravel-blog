<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();

        Post::factory()
            ->count(10)
            ->state(fn () => [
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
            ])
            ->create()
            ->each(fn (Post $post) => $post->tags()->attach($tags->random(2)));
    }
}
