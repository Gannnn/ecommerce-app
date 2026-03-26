<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getProducts(
            search: $request->query('search'),
            category: $request->query('category'),
        );

        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        return response()->json($product);
    }
}
