<?php

namespace App\Services;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function getCart(?string $sessionId, ?int $userId): Collection
    {
        return $this->cartRepository->getCart($sessionId, $userId);
    }

    public function addItem(?string $sessionId, int $productId, int $quantity, ?int $userId = null): CartItem
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            abort(404, 'Product not found.');
        }

        $existing = $this->cartRepository->findItem($sessionId, $productId, $userId);
        $currentQty = $existing ? $existing->quantity : 0;

        if (($currentQty + $quantity) > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$product->stock_quantity} units available.",
            ]);
        }

        return $this->cartRepository->addItem($sessionId, $productId, $quantity, $userId);
    }

    public function updateItem(?string $sessionId, ?int $userId, int $cartItemId, int $quantity): CartItem
    {
        $query = CartItem::where('id', $cartItemId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $item = $query->firstOrFail();

        if ($quantity > $item->product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$item->product->stock_quantity} units available.",
            ]);
        }

        return $this->cartRepository->updateItem($cartItemId, $quantity);
    }

    public function removeItem(?string $sessionId, ?int $userId, int $cartItemId): void
    {
        $query = CartItem::where('id', $cartItemId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $query->firstOrFail();

        $this->cartRepository->removeItem($cartItemId);
    }

    public function clearCart(?string $sessionId, ?int $userId): void
    {
        $this->cartRepository->clearCart($sessionId, $userId);
    }

    public function mergeGuestCart(string $sessionId, int $userId): void
    {
        $this->cartRepository->mergeToUser($sessionId, $userId);
    }
}
