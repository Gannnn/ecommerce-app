<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminCurrencyController extends Controller
{
    public function index(): JsonResponse
    {
        $currencies = Currency::orderByDesc('is_default')
            ->orderBy('code')
            ->get();

        return response()->json($currencies);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $currency = Currency::findOrFail($id);

        if ($currency->is_default) {
            return response()->json(['message' => 'The default currency cannot be modified.'], 422);
        }

        $data = $request->validate([
            'name'      => 'sometimes|string|max:100',
            'symbol'    => 'sometimes|nullable|string|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $currency->update($data);

        return response()->json($currency);
    }

    public function sync(): JsonResponse
    {
        $exitCode = Artisan::call('bnm:sync-exchange-rates');

        if ($exitCode !== 0) {
            return response()->json(['message' => 'Sync failed. Check server logs.'], 500);
        }

        $currencies = Currency::orderByDesc('is_default')->orderBy('code')->get();

        return response()->json([
            'message'    => 'Exchange rates synced successfully.',
            'currencies' => $currencies,
        ]);
    }
}
