<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with(['category', 'media'])->latest()->get();
        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id'    => 'required|exists:product_categories,id',
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_featured'    => 'nullable|boolean',
            'image'          => 'nullable|image|max:5120',
        ]);

        $product = Product::create([
            'category_id'    => $data['category_id'],
            'name'           => $data['name'],
            'slug'           => Str::slug($data['name']),
            'description'    => $data['description'],
            'price'          => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
            'is_featured'    => $data['is_featured'] ?? false,
        ]);

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return response()->json($product->fresh(['category', 'media']), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Product::with(['category', 'media'])->findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'category_id'    => 'sometimes|exists:product_categories,id',
            'name'           => 'sometimes|string|max:255',
            'description'    => 'sometimes|string',
            'price'          => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'is_featured'    => 'nullable|boolean',
            'image'          => 'nullable|image|max:5120',
        ]);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update(collect($data)->except('image')->toArray());

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return response()->json($product->fresh(['category', 'media']));
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->clearMediaCollection('images');
        $product->delete();

        return response()->json(['message' => 'Product deleted.']);
    }
}
