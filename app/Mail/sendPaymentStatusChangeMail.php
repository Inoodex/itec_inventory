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

class sendPaymentStatusChangeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $orderNumber;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderNumber, $data)
    {
        $this->orderNumber = $orderNumber;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $id = $this->orderNumber;

        $order = Order::leftJoin('users', 'users.id', '=', 'orders.customer_id')
        ->select('orders.*','users.name', 'users.email')
        ->where('orders.order_number', $id)->first();

        $billing = Address::where('id',  $order->billing_address)->first();
        $shipping = Address::where('id',  $order->shipping_address)->first();
        $items = OrderItem::where('order_items.order_number', $id)->get();
        $lib_districts = lib_districts();
        $lib_areas = lib_areas();

        return $this->view('layouts.sendPaymentStatusMail', compact('order','items', 'billing', 'shipping','lib_districts','lib_areas'))
            ->subject('Payment Status Update');
    }
}
