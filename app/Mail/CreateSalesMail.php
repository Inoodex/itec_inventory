<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreateSalesMail extends Mailable
{
    use Queueable, SerializesModels;
    public $service;

    /**
     * Create a new message instance.
     */
    public function __construct($service)
    {
       $this->service = $service;
    }

    public function build()
    {
        $service = $this->service;

    
        return $this->view('layouts.createSalesMail', compact('service'))
                    ->subject('Sales Confirmation');
    }
}
