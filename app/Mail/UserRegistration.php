<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistration extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $body = null;
    /**
     * Create a new message instance.
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    public function build()
    {
        return $this->subject('Welcome!')->view('emails.welcome');
    }
}
