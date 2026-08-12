<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlertEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Product $product,
        public int $currentStock,
        public int $threshold = 5
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('inventory-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.low';
    }

    public function broadcastWith(): array
    {
        return [
            'product_id'    => $this->product->id,
            'product_name'  => $this->product->name,
            'current_stock' => $this->currentStock,
            'threshold'     => $this->threshold,
            'message'       => "Low stock alert: {$this->product->name} has only {$this->currentStock} items remaining!",
        ];
    }
}
