<?php

namespace App\Http\Controllers;

use App\Models\OrderHistory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    // Ambil semua history dari sebuah order
    public function index($orderId)
    {
        $orderHistories = OrderHistory::with('user')
            ->where('order_id', $orderId)
            ->get();

        return response()->json($orderHistories);
    }

    // Tambah history baru
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string',
            'notes'  => 'nullable|string',
        ]);

        // pastikan order ada
        $order = Order::findOrFail($orderId);

        $history = OrderHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'status'     => $request->status,
            'notes'      => $request->notes,
        ]);

        return response()->json([
            'message' => 'Order history added successfully',
            'history' => $history
        ], 201);
    }

    // Detail history
    public function show($id)
    {
        $history = OrderHistory::with(['order', 'user'])->findOrFail($id);
        return response()->json($history);
    }

    // Update history (opsional, biasanya tidak perlu, tapi aku bikin aja)
    public function update(Request $request, $id)
    {
        $history = OrderHistory::findOrFail($id);

        $request->validate([
            'status' => 'sometimes|string',
            'notes'  => 'nullable|string',
        ]);

        $history->update([
            'status' => $request->status ?? $history->status,
            'notes'  => $request->notes ?? $history->notes,
        ]);

        return response()->json([
            'message' => 'Order history updated successfully',
            'history' => $history
        ]);
    }

    // Hapus history (opsional, biasanya log tidak dihapus)
    public function destroy($id)
    {
        $history = OrderHistory::findOrFail($id);
        $history->delete();

        return response()->json(['message' => 'Order history deleted successfully']);
    }
}