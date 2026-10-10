<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(Post $post): array
    {
        return [
            'title' => 'Changed',
            'short_content' => 'Changed',
            'content' => 'Changed',
            'category_id' => $post->category_id,
        ];
    }

    public function test_guest_is_redirected_to_login_for_protected_post_routes(): void
    {
        $post = Post::factory()->create();

        $this->get(route('posts.create'))->assertRedirect(route('login'));
        $this->post(route('posts.store'), $this->validPayload($post))->assertRedirect(route('login'));
        $this->get(route('posts.edit', $post))->assertRedirect(route('login'));
        $this->put(route('posts.update', $post), $this->validPayload($post))->assertRedirect(route('login'));
        $this->delete(route('posts.destroy', $post))->assertRedirect(route('login'));

        $this->assertNotSame('Changed', $post->fresh()->title);
        $this->assertNotSoftDeleted($post);
    }

    public function test_other_user_gets_403_on_edit_update_and_delete(): void
    {
        $post = Post::factory()->create();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get(route('posts.edit', $post))->assertForbidden();
        $this->actingAs($intruder)->put(route('posts.update', $post), $this->validPayload($post))->assertForbidden();
        $this->actingAs($intruder)->delete(route('posts.destroy', $post))->assertForbidden();

        $this->assertNotSame('Changed', $post->fresh()->title);
        $this->assertNotSoftDeleted($post);
    }

    public function test_owner_is_allowed_on_edit_update_and_delete(): void
    {
        $post = Post::factory()->create();
        $owner = $post->user;

        $this->actingAs($owner)->get(route('posts.edit', $post))->assertOk();
        $this->actingAs($owner)->put(route('posts.update', $post), $this->validPayload($post))->assertRedirect();
        $this->actingAs($owner)->delete(route('posts.destroy', $post))->assertRedirect();

        $this->assertSoftDeleted($post);
    }

    public function test_edit_and_delete_buttons_are_only_visible_to_the_owner(): void
    {
        $post = Post::factory()->create();

        $this->actingAs($post->user)->get(route('posts.show', $post))
            ->assertSee(route('posts.edit', $post));
        $this->actingAs(User::factory()->create())->get(route('posts.show', $post))
            ->assertDontSee(route('posts.edit', $post));
        $this->get(route('posts.show', $post))->assertDontSee(route('posts.edit', $post));
    }
}
