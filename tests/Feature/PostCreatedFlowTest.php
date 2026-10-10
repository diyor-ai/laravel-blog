<?php

namespace Tests\Feature;

use App\Events\PostCreated;
use App\Listeners\SendPostCreatedMail;
use App\Mail\PostCreatedMail;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PostCreatedFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createPostRequest(User $user)
    {
        return $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'Title',
            'short_content' => 'Short',
            'content' => 'Content',
            'category_id' => Category::factory()->create()->id,
        ]);
    }

    public function test_storing_a_post_dispatches_the_event(): void
    {
        Event::fake([PostCreated::class]);

        $this->createPostRequest(User::factory()->create());

        Event::assertDispatched(PostCreated::class, fn (PostCreated $e) => $e->post->title === 'Title');
    }

    public function test_event_has_the_mail_listener_registered(): void
    {
        Event::fake();

        Event::assertListening(PostCreated::class, SendPostCreatedMail::class);
    }

    public function test_listener_is_queued_on_the_mails_queue(): void
    {
        Queue::fake();

        $this->createPostRequest(User::factory()->create());

        Queue::assertPushedOn('mails', \Illuminate\Events\CallQueuedListener::class, function ($job) {
            return $job->class === SendPostCreatedMail::class;
        });
    }

    public function test_listener_queues_the_mail_for_the_author(): void
    {
        Mail::fake();
        $post = Post::factory()->create();

        (new SendPostCreatedMail)->handle(new PostCreated($post));

        Mail::assertQueued(PostCreatedMail::class, function (PostCreatedMail $mail) use ($post) {
            return $mail->hasTo($post->user->email) && $mail->queue === 'mails';
        });
    }

    public function test_full_flow_ends_with_a_queued_mail(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        // QUEUE_CONNECTION=sync in phpunit.xml: the queued listener runs inline.
        $this->createPostRequest($user);

        Mail::assertQueuedCount(1);
        Mail::assertQueued(PostCreatedMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_mail_renders_post_details(): void
    {
        $post = Post::factory()->create(['title' => 'Mail title']);

        $mail = new PostCreatedMail($post);

        $mail->assertSeeInHtml('Mail title');
        $mail->assertSeeInHtml($post->user->name);
    }
}
