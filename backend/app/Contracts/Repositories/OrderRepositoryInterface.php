<?php

namespace App\Contracts\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function all(): Collection;

    public function forUser(int $userId): Collection;

    public function find(int $id): ?Order;

    public function create(array $data, array $items): Order;

    public function updateStatus(int $id, string $status): Order;
}
