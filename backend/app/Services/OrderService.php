<?php

namespace App\Services;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CartRepositoryInterface $cartRepository
    ) {}

    public function getUserOrders(int $userId): Collection
    {
        return $this->orderRepository->forUser($userId);
    }

    public function getOrder(int $id, int $userId): Order
    {
        $order = $this->orderRepository->find($id);

        if (!$order || $order->user_id !== $userId) {
            abort(404, 'Order not found.');
        }

        return $order;
    }

    public function placeOrder(string $sessionId, int $userId, ?string $notes = null): Order
    {
        $cartItems = $this->cartRepository->getBySession($sessionId);

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $orderItems = $cartItems->map(function ($item) {
            return [
                'product_id'    => $item->product_id,
                'product_name'  => $item->product->name,
                'product_price' => $item->product->price,
                'quantity'      => $item->quantity,
                'subtotal'      => round($item->product->price * $item->quantity, 2),
            ];
        })->toArray();

        $subtotal = collect($orderItems)->sum('subtotal');

        $order = $this->orderRepository->create([
            'user_id'  => $userId,
            'status'   => 'pending',
            'subtotal' => $subtotal,
            'total'    => $subtotal,
            'notes'    => $notes,
        ], $orderItems);

        $this->cartRepository->clearSession($sessionId);

        return $order;
    }

    public function updateStatus(int $orderId, int $userId, string $status): Order
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order || $order->user_id !== $userId) {
            abort(404, 'Order not found.');
        }

        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) {
            throw ValidationException::withMessages(['status' => 'Invalid status.']);
        }

        return $this->orderRepository->updateStatus($orderId, $status);
    }
}
