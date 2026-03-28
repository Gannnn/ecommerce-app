<?php

namespace App\Contracts\Repositories;

use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

interface CartRepositoryInterface
{
    public function getCart(?string $sessionId, ?int $userId): Collection;

    public function findItem(?string $sessionId, int $productId, ?int $userId = null): ?CartItem;

    public function addItem(?string $sessionId, int $productId, int $quantity, ?int $userId = null): CartItem;

    public function updateItem(int $cartItemId, int $quantity): CartItem;

    public function removeItem(int $cartItemId): void;

    public function clearCart(?string $sessionId, ?int $userId): void;

    public function mergeToUser(string $sessionId, int $userId): void;
}
