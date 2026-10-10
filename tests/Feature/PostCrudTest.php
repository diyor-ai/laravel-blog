<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostCrudTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Hello world',
            'short_content' => 'Short',
            'content' => 'Long content',
            'category_id' => Category::factory()->create()->id,
        ], $overrides);
    }

    public function test_index_and_show_are_public(): void
    {
        $post = Post::factory()->create(['title' => 'Visible title']);

        $this->get(route('posts.index'))->assertOk()->assertSee('Visible title');
        $this->get(route('posts.show', $post))->assertOk()->assertSee('Visible title');
    }

    public function test_show_lists_at_most_five_recent_posts_excluding_current(): void
    {
        $posts = Post::factory()->count(8)->create();
        $current = $posts->first();

        $recent = $this->get(route('posts.show', $current))->viewData('recent_posts');

        $this->assertCount(5, $recent);
        $this->assertFalse($recent->contains('id', $current->id));
    }

    public function test_authenticated_user_can_create_post_with_tags_and_photo(): void
    {
        Storage::fake('public');
        Mail::fake();
        $user = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $response = $this->actingAs($user)->post(route('posts.store'), $this->payload([
            'tags' => $tags->pluck('id')->all(),
            'photo' => UploadedFile::fake()->image('cover.jpg'),
        ]));

        $response->assertRedirect(route('posts.index'));
        $post = Post::firstOrFail();
        $this->assertSame($user->id, $post->user_id);
        $this->assertCount(2, $post->tags);
        Storage::disk('public')->assertExists($post->photo);
    }

    public function test_uploaded_files_get_hashed_names_and_do_not_collide(): void
    {
        Storage::fake('public');
        Mail::fake();
        $user = User::factory()->create();

        foreach (range(1, 2) as $i) {
            $this->actingAs($user)->post(route('posts.store'), $this->payload([
                'photo' => UploadedFile::fake()->image('same-name.jpg'),
            ]));
        }

        [$first, $second] = Post::orderBy('id')->get();

        $this->assertNotSame($first->photo, $second->photo);
        $this->assertStringNotContainsString('same-name', $first->photo);
        Storage::disk('public')->assertExists([$first->photo, $second->photo]);
    }

    public function test_owner_can_view_edit_form_and_update_post_including_category_and_tags(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $post->tags()->attach(Tag::factory()->count(2)->create());
        $newCategory = Category::factory()->create();
        $newTag = Tag::factory()->create();

        $this->actingAs($user)->get(route('posts.edit', $post))->assertOk();

        $this->actingAs($user)->put(route('posts.update', $post), $this->payload([
            'title' => 'Updated title',
            'category_id' => $newCategory->id,
            'tags' => [$newTag->id],
        ]))->assertRedirect(route('posts.show', $post));

        $post->refresh();
        $this->assertSame('Updated title', $post->title);
        $this->assertSame($newCategory->id, $post->category_id);
        $this->assertSame([$newTag->id], $post->tags()->pluck('tags.id')->all());
    }

    public function test_updating_photo_replaces_the_old_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $old = UploadedFile::fake()->image('old.jpg')->store('post-photos', 'public');
        $post = Post::factory()->for($user)->create(['photo' => $old]);

        $this->actingAs($user)->put(route('posts.update', $post), $this->payload([
            'photo' => UploadedFile::fake()->image('new.jpg'),
        ]));

        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($post->refresh()->photo);
    }

    public function test_deleting_a_post_is_a_soft_delete(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->actingAs($user)->delete(route('posts.destroy', $post))
            ->assertRedirect(route('posts.index'));

        $this->assertSoftDeleted($post);
        $this->get(route('posts.show', $post))->assertNotFound();
        $this->assertSame(0, Post::count());
        $this->assertSame(1, Post::withTrashed()->count());
    }

    public function test_index_is_paginated_six_per_page(): void
    {
        Post::factory()->count(8)->create();

        $this->assertCount(6, $this->get(route('posts.index'))->viewData('posts'));
        $this->assertCount(2, $this->get(route('posts.index', ['page' => 2]))->viewData('posts'));
    }
}
