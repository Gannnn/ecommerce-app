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
            'notes'         => 'nullable|string|max:500',
            'currency_code' => 'nullable|string|size:3',
            'currency_rate' => 'nullable|numeric|min:0',
            'currency_unit' => 'nullable|integer|min:1',
        ]);

        $order = $this->orderService->placeOrder(
            $request->user()->id,
            $data['notes'] ?? null,
            $data['currency_code'] ?? 'MYR',
            isset($data['currency_rate']) ? (float) $data['currency_rate'] : null,
            $data['currency_unit'] ?? 1,
        );

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
