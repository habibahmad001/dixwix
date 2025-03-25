<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignupRewardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * SignupRewardMail constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Sign Up Reward Points!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.signup_reward',
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

    public function build()
    {
        return $this->subject('🎉 Sign Up Reward Points!')
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')) // Important for authentication
            ->replyTo(env('MAIL_FROM_ADDRESS')) // Optional
            ->view('emails.signup_reward')
            ->with($this->data); // Pass entire data array to the view
    }
}
