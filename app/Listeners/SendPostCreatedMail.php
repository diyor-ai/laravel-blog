<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Mail\PostCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPostCreatedMail implements ShouldQueue
{
    public string $queue = 'mails';

    public function handle(PostCreated $event): void
    {
        Mail::to($event->post->user)->queue(new PostCreatedMail($event->post));
    }
}
