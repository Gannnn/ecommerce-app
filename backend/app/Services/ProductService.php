<?php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function getProducts(?string $search = null, ?string $category = null, ?string $sort = null): Collection
    {
        $filters = array_filter([
            'search'   => $search,
            'category' => $category,
            'sort'     => $sort,
        ]);

        return $this->productRepository->all($filters);
    }

    public function getProduct(string $slug): Product
    {
        $product = $this->productRepository->findBySlug($slug);

        if (!$product) {
            abort(404, 'Product not found.');
        }

        return $product;
    }
}
