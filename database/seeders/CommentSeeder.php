<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        Post::all()->each(fn (Post $post) => Comment::factory()
            ->count(2)
            ->create(['post_id' => $post->id, 'user_id' => $users->random()->id]));
    }
}
