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

    public function getCart(string $sessionId): Collection
    {
        return $this->cartRepository->getBySession($sessionId);
    }

    public function addItem(string $sessionId, int $productId, int $quantity, ?int $userId = null): CartItem
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            abort(404, 'Product not found.');
        }

        $existing = $this->cartRepository->findItem($sessionId, $productId);
        $currentQty = $existing ? $existing->quantity : 0;

        if (($currentQty + $quantity) > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$product->stock_quantity} units available.",
            ]);
        }

        return $this->cartRepository->addItem($sessionId, $productId, $quantity, $userId);
    }

    public function updateItem(string $sessionId, int $cartItemId, int $quantity): CartItem
    {
        $item = CartItem::where('id', $cartItemId)
            ->where('session_id', $sessionId)
            ->firstOrFail();

        if ($quantity > $item->product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$item->product->stock_quantity} units available.",
            ]);
        }

        return $this->cartRepository->updateItem($cartItemId, $quantity);
    }

    public function removeItem(string $sessionId, int $cartItemId): void
    {
        CartItem::where('id', $cartItemId)
            ->where('session_id', $sessionId)
            ->firstOrFail();

        $this->cartRepository->removeItem($cartItemId);
    }

    public function clearCart(string $sessionId): void
    {
        $this->cartRepository->clearSession($sessionId);
    }

    public function mergeGuestCart(string $sessionId, int $userId): void
    {
        $this->cartRepository->mergeToUser($sessionId, $userId);
    }
}
