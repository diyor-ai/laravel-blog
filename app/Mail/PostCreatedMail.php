<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Post $post)
    {
        $this->onQueue('mails');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your post was created');
    }

    public function content(): Content
    {
        return new Content(view: 'mails.post-created');
    }
}
