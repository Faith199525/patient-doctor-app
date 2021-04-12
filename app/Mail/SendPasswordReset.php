<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param $token
     */
    public function __construct($event)
    {
        $this->user = $event->user;
        $this->url = env('APP_DOMAIN','https://drcallaway.ng') . '/auth/forgotpassword/' . $event->token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.password_reset');
    }
}
