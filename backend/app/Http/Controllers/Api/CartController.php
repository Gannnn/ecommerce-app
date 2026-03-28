<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        $sessionId = $request->header('X-Session-ID');
        $userId = Auth::guard('sanctum')->user()?->id;
        $items = $this->cartService->getCart($sessionId, $userId);

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $sessionId = $request->header('X-Session-ID');
        $userId = Auth::guard('sanctum')->user()?->id;

        $item = $this->cartService->addItem($sessionId, $data['product_id'], $data['quantity'], $userId);

        return response()->json($item, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = $request->header('X-Session-ID');
        $userId = Auth::guard('sanctum')->user()?->id;
        $item = $this->cartService->updateItem($sessionId, $userId, $id, $data['quantity']);

        return response()->json($item);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $sessionId = $request->header('X-Session-ID');
        $userId = Auth::guard('sanctum')->user()?->id;
        $this->cartService->removeItem($sessionId, $userId, $id);

        return response()->json(['message' => 'Item removed.']);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $sessionId = $request->header('X-Session-ID');
        $userId = Auth::guard('sanctum')->user()?->id;
        $this->cartService->clearCart($sessionId, $userId);

        return response()->json(['message' => 'Cart cleared.']);
    }
}
