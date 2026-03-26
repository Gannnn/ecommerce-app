<?php

namespace App\Contracts\Repositories;

use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

interface CartRepositoryInterface
{
    public function getBySession(string $sessionId): Collection;

    public function findItem(string $sessionId, int $productId): ?CartItem;

    public function addItem(string $sessionId, int $productId, int $quantity, ?int $userId = null): CartItem;

    public function updateItem(int $cartItemId, int $quantity): CartItem;

    public function removeItem(int $cartItemId): void;

    public function clearSession(string $sessionId): void;

    public function mergeToUser(string $sessionId, int $userId): void;
}
