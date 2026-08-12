<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Sale $sale) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('sales-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sale.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id'          => $this->sale->id,
            'order_no'    => $this->sale->order_no,
            'payble'      => $this->sale->payble,
            'customer'    => $this->sale->customer?->name,
            'created_at'  => $this->sale->created_at?->toIso8601String(),
        ];
    }
}
