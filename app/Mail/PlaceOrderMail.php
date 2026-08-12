<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\Admin\Toping;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ProductTag;

class PlaceOrderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $service;
    public $serviceMans;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($service, $serviceMans)
    {
        $this->service = $service;
        $this->serviceMans = $serviceMans;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $service = $this->service;
        $serviceMans = $this->serviceMans;

        if($service->status=='1') $subject = "Service Completed";
        else $subject = "Service Confirmation.";

        return $this->view('layouts.placeOrderMail', compact('service','serviceMans'))
                    ->subject($subject);
    }
}


