<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostValidationTest extends TestCase
{
    use RefreshDatabase;

    private function submit(array $data)
    {
        return $this->actingAs(User::factory()->create())
            ->post(route('posts.store'), array_merge([
                'title' => 'Title',
                'short_content' => 'Short',
                'content' => 'Content',
                'category_id' => Category::factory()->create()->id,
            ], $data));
    }

    public function test_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('posts.store'), [])
            ->assertSessionHasErrors(['title', 'short_content', 'content', 'category_id']);

        $this->assertSame(0, Post::count());
    }

    public function test_category_must_exist(): void
    {
        $this->submit(['category_id' => 9999])->assertSessionHasErrors('category_id');
    }

    public function test_tags_must_be_an_array_of_existing_ids(): void
    {
        $this->submit(['tags' => 'not-an-array'])->assertSessionHasErrors('tags');
        $this->submit(['tags' => [9999]])->assertSessionHasErrors('tags.0');
    }

    public function test_photo_must_be_an_image(): void
    {
        $this->submit(['photo' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('photo');
    }

    public function test_photo_must_not_exceed_two_megabytes(): void
    {
        Storage::fake('public');

        $this->submit(['photo' => UploadedFile::fake()->image('big.jpg')->size(3000)])
            ->assertSessionHasErrors('photo');
    }

    public function test_update_uses_the_same_validation(): void
    {
        $post = Post::factory()->create();

        $this->actingAs($post->user)
            ->put(route('posts.update', $post), ['title' => '', 'category_id' => 9999])
            ->assertSessionHasErrors(['title', 'short_content', 'content', 'category_id']);
    }
}
