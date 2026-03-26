<?php

namespace App\Repositories;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

class CartRepository implements CartRepositoryInterface
{
    public function getBySession(string $sessionId): Collection
    {
        return CartItem::with('product')
            ->where('session_id', $sessionId)
            ->get();
    }

    public function findItem(string $sessionId, int $productId): ?CartItem
    {
        return CartItem::where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->first();
    }

    public function addItem(string $sessionId, int $productId, int $quantity, ?int $userId = null): CartItem
    {
        $existing = $this->findItem($sessionId, $productId);

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing->fresh('product');
        }

        return CartItem::create([
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity'   => $quantity,
            'user_id'    => $userId,
        ])->load('product');
    }

    public function updateItem(int $cartItemId, int $quantity): CartItem
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->update(['quantity' => $quantity]);
        return $item->load('product');
    }

    public function removeItem(int $cartItemId): void
    {
        CartItem::destroy($cartItemId);
    }

    public function clearSession(string $sessionId): void
    {
        CartItem::where('session_id', $sessionId)->delete();
    }

    public function mergeToUser(string $sessionId, int $userId): void
    {
        $guestItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::where('session_id', $sessionId)
                ->where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update(['user_id' => $userId]);
            }
        }
    }
}
