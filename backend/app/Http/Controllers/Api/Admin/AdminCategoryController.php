<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ProductCategory::withCount('products')->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $category = ProductCategory::create($data);

        return response()->json($category, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = ProductCategory::findOrFail($id);

        $data = $request->validate([
            'name'        => "sometimes|string|max:255|unique:product_categories,name,{$id}",
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
        ]);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return response()->json($category->fresh());
    }

    public function destroy(int $id): JsonResponse
    {
        ProductCategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Category deleted.']);
    }
}
