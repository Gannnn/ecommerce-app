<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->getUserOrders($request->user()->id);

        return response()->json($orders);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getOrder($id, $request->user()->id);

        return response()->json($order);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $sessionId = $request->header('X-Session-ID', '');
        $order = $this->orderService->placeOrder($sessionId, $request->user()->id, $data['notes'] ?? null);

        return response()->json($order, 201);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = $this->orderService->updateStatus($id, $request->user()->id, $data['status']);

        return response()->json($order);
    }
}
