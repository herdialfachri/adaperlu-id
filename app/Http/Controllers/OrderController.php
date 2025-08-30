<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Index: daftar order sesuai role
    public function index()
    {
        $user = Auth::user();
        $query = Order::with(['user', 'service', 'worker', 'orderHistories', 'ratings']);

        if ($user->role_id == 2) {
            $query->where('user_id', $user->id); // customer
        } elseif ($user->role_id == 3) {
            $query->where('worker_id', $user->id); // worker
        }

        return response()->json($query->get());
    }

    // Buat order (customer only → role_id:2 via middleware)
    public function store(Request $request)
    {
        $request->validate([
            'service_id'     => 'required|exists:services,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'notes'          => 'nullable|string',
            'payment_method' => 'required|string',
            'address'        => 'required|string',
        ]);

        $service    = Service::findOrFail($request->service_id);
        $unitPrice  = $service->price;
        $days       = \Carbon\Carbon::parse($request->start_date)
            ->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1;
        $totalPrice = $unitPrice * $days;

        $order = Order::create([
            'user_id'        => Auth::id(),
            'worker_id'      => $service->user_id,
            'service_id'     => $request->service_id,
            'status'         => 'pending',
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'unit_price'     => $unitPrice,
            'total_price'    => $totalPrice,
            'notes'          => $request->notes,
            'payment_status' => 'unpaid',
            'payment_method' => $request->payment_method,
        ]);

        // History pertama kali
        OrderHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'status'     => 'pending',
            'notes'      => 'Order dibuat',
        ]);

        return response()->json(['message' => 'Order created', 'order' => $order], 201);
    }

    // Detail order
    public function show($id)
    {
        $order = Order::with(['user', 'service', 'worker', 'orderHistories', 'ratings'])
            ->findOrFail($id);

        return response()->json($order);
    }

    // Worker menerima/menolak order (role_id:3 via middleware)
    public function workerAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        $order = Order::findOrFail($id);

        if ($request->action === 'accept') {
            $order->update(['status' => 'confirmed']);
            $message = 'Order accepted';
        } else {
            $order->update(['status' => 'cancelled']);
            $message = 'Order rejected';
        }

        // Update history terakhir
        $history = OrderHistory::where('order_id', $order->id)->latest('id')->first();
        if ($history) {
            $history->update([
                'changed_by' => Auth::id(),
                'status'     => $order->status,
                'notes'      => "Order {$request->action}ed by worker",
            ]);
        }

        return response()->json([
            'message' => $message,
            'order'   => $order,
        ]);
    }

    // Admin update order (role_id:1 via middleware)
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status'         => 'sometimes|in:pending,confirmed,in_progress,completed,cancelled',
            'payment_status' => 'sometimes|in:unpaid,paid,failed',
            'notes'          => 'nullable|string',
        ]);

        $order->update($request->only('status', 'payment_status', 'notes'));

        // Kalau admin bayar dan status masih confirmed → otomatis in_progress
        if ($order->payment_status === 'paid' && $order->status === 'confirmed') {
            $order->status = 'in_progress';
            $order->save();

            // Update history terakhir
            $history = OrderHistory::where('order_id', $order->id)->latest('id')->first();
            if ($history) {
                $history->update([
                    'changed_by' => Auth::id(),
                    'status'     => 'in_progress',
                    'notes'      => 'Payment received, order in progress',
                ]);
            }
        } else {
            // Update history terakhir (kecuali completed)
            if ($order->status !== 'completed') {
                $history = OrderHistory::where('order_id', $order->id)->latest('id')->first();
                if ($history) {
                    $history->update([
                        'changed_by' => Auth::id(),
                        'status'     => $order->status,
                        'notes'      => $request->notes ?? 'Order updated by admin',
                    ]);
                }
            } else {
                // Kalau completed → bikin history baru
                OrderHistory::create([
                    'order_id'   => $order->id,
                    'changed_by' => Auth::id(),
                    'status'     => 'completed',
                    'notes'      => $request->notes ?? 'Order completed',
                ]);
            }
        }

        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }

    // User menandai order selesai
    public function markCompleted($id)
    {
        $order = Order::findOrFail($id);
        $user = Auth::user();

        if ($user->role_id != 2) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($order->user_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // update status
        $order->update(['status' => 'completed']);

        // masukin ke history
        OrderHistory::create([
            'order_id'   => $order->id,
            'changed_by' => $user->id,
            'status'     => 'completed',
            'notes'      => 'Order selesai oleh user',
        ]);

        return response()->json([
            'message' => 'Order marked as completed',
            'order'   => $order
        ]);
    }
}
