<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetTest extends Mailable
{
    use Queueable, SerializesModels;

    public $actionUrl;
    public $count;

    /**
     * Create a new message instance.
     */
    public function __construct($actionUrl, $count = 60)
    {
        $this->actionUrl = $actionUrl;
        $this->count = $count;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Recuperação de Senha - Gateway')
            ->view('emails.password-reset-simple');
    }
}
