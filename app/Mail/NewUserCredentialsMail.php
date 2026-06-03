<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class NewUserCredentialsMail extends Mailable
{
    // stores user and password
    public $user;
    public $plainPassword;

    public function __construct($user, $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        return $this->subject('Your ResolveIT Account Credentials')
                    ->view('emails.new-user');
    }
}