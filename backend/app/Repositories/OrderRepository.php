<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function forUser(int $userId): Collection
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function find(int $id): ?Order
    {
        return Order::with('items.product')->find($id);
    }

    public function create(array $data, array $items): Order
    {
        return DB::transaction(function () use ($data, $items) {
            $order = Order::create($data);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['product_name'],
                    'product_price' => $item['product_price'],
                    'quantity'      => $item['quantity'],
                    'subtotal'      => $item['subtotal'],
                ]);
            }

            return $order->load('items.product');
        });
    }

    public function updateStatus(int $id, string $status): Order
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);
        return $order->load('items.product');
    }
}
