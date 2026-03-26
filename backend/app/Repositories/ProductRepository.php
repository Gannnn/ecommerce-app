<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        $query = Product::with(['category', 'media']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->whereHas('category', fn($q) => $q->where('slug', $filters['category']));
        }

        match ($filters['sort'] ?? 'newest') {
            'price-asc'  => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            default      => $query->orderByDesc('created_at'),
        };

        return $query->get();
    }

    public function find(int $id): ?Product
    {
        return Product::with(['category', 'media'])->find($id);
    }
}
