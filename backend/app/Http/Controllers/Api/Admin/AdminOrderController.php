<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): JsonResponse
    {
        return response()->json($this->orderService->getAllOrders());
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = $this->orderService->adminUpdateStatus($id, $data['status']);

        return response()->json($order);
    }
}
