<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pinCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pinCode)
    {
        $this->pinCode = $pinCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pinCode = $this->pinCode;
        return $this->view('layouts.verificationMail',compact('pinCode'))
                    ->subject('Verification code');
    }
}

