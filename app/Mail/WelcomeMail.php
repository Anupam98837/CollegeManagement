<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $msg;
    public string $sub;

    /**
     * Create a new message instance.
     *
     * @param  string  $msg  The body text for the email
     * @param  string  $sub  The subject line
     */
    public function __construct(string $msg, string $sub)
    {
        $this->msg = $msg;
        $this->sub = $sub;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->sub,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',        // put your Blade here (e.g. resources/views/emails/welcome.blade.php)
            with: [
                'messageText' => $this->msg,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
