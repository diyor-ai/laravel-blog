<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_comment(): void
    {
        $post = Post::factory()->create();

        $this->post(route('comments.store'), ['body' => 'Hi', 'post_id' => $post->id])
            ->assertRedirect(route('login'));

        $this->assertSame(0, Comment::count());
    }

    public function test_authenticated_user_can_comment_and_it_is_shown(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('posts.show', $post))
            ->post(route('comments.store'), ['body' => 'Great post', 'post_id' => $post->id])
            ->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('comments', ['body' => 'Great post', 'user_id' => $user->id, 'post_id' => $post->id]);
        $this->get(route('posts.show', $post))->assertSee('Great post');
    }

    public function test_comment_is_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('comments.store'), [])
            ->assertSessionHasErrors(['body', 'post_id']);
        $this->actingAs($user)->post(route('comments.store'), ['body' => 'x', 'post_id' => 9999])
            ->assertSessionHasErrors('post_id');
        $this->actingAs($user)
            ->post(route('comments.store'), ['body' => str_repeat('a', 1001), 'post_id' => Post::factory()->create()->id])
            ->assertSessionHasErrors('body');
    }

    public function test_only_the_store_route_exists(): void
    {
        $this->assertTrue(Route::has('comments.store'));

        foreach (['index', 'create', 'show', 'edit', 'update', 'destroy'] as $action) {
            $this->assertFalse(Route::has("comments.$action"), "comments.$action should not be registered");
        }
    }
}
