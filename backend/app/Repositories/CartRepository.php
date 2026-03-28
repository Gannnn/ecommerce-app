<?php

namespace App\Repositories;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

class CartRepository implements CartRepositoryInterface
{
    public function getCart(?string $sessionId, ?int $userId): Collection
    {
        if ($userId) {
            return CartItem::with('product')->where('user_id', $userId)->get();
        }

        return CartItem::with('product')
            ->where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();
    }

    public function findItem(?string $sessionId, int $productId, ?int $userId = null): ?CartItem
    {
        if ($userId) {
            return CartItem::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();
        }

        return CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->where('product_id', $productId)
            ->first();
    }

    public function addItem(?string $sessionId, int $productId, int $quantity, ?int $userId = null): CartItem
    {
        $existing = $this->findItem($sessionId, $productId, $userId);

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing->fresh('product');
        }

        return CartItem::create([
            'session_id' => $userId ? null : $sessionId,
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

    public function clearCart(?string $sessionId, ?int $userId): void
    {
        if ($userId) {
            CartItem::where('user_id', $userId)->delete();
        } else {
            CartItem::where('session_id', $sessionId)->whereNull('user_id')->delete();
        }
    }

    public function mergeToUser(string $sessionId, int $userId): void
    {
        $guestItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update(['user_id' => $userId, 'session_id' => null]);
            }
        }
    }
}
