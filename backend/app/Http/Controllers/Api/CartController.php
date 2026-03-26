<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        $sessionId = $request->header('X-Session-ID', '');
        $items = $this->cartService->getCart($sessionId);

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $sessionId = $request->header('X-Session-ID', '');
        $userId = $request->user()?->id;

        $item = $this->cartService->addItem($sessionId, $data['product_id'], $data['quantity'], $userId);

        return response()->json($item, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = $request->header('X-Session-ID', '');
        $item = $this->cartService->updateItem($sessionId, $id, $data['quantity']);

        return response()->json($item);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $sessionId = $request->header('X-Session-ID', '');
        $this->cartService->removeItem($sessionId, $id);

        return response()->json(['message' => 'Item removed.']);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $sessionId = $request->header('X-Session-ID', '');
        $this->cartService->clearCart($sessionId);

        return response()->json(['message' => 'Cart cleared.']);
    }
}
