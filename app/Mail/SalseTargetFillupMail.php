<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalseTargetFillupMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $month;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $month)
    {
        $this->name = $name;
        $this->month = $month;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->name;
        $month = $this->month;

        

        return $this->view('layouts.salestargetFillupMail', compact('name','month'))
            ->subject('Congratulations on Achieving Your Target!');
    }
}
