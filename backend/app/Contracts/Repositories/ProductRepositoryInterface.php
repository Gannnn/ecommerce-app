<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(array $filters = []): Collection;

    public function find(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;
}
