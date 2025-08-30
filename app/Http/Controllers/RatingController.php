<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    // Ambil semua rating untuk 1 service
    public function index($service_id)
    {
        $ratings = Rating::with('user')
            ->where('service_id', $service_id)
            ->get();

        return response()->json($ratings);
    }

    // User kasih rating
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string',
        ]);

        // cek order valid
        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('service_id', $request->service_id)
            ->where('status', 'completed') // cuma kalau order selesai
            ->first();

        if (!$order) {
            return response()->json(['message' => 'You can only rate after completing this order'], 403);
        }

        // cek kalau udah pernah rating
        if (Rating::where('order_id', $request->order_id)->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'You already rated this order'], 422);
        }

        $rating = Rating::create([
            'user_id'    => Auth::id(),
            'service_id' => $request->service_id,
            'order_id'   => $request->order_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        // update rata-rata rating service
        $avg = Rating::where('service_id', $request->service_id)->avg('rating');
        Service::where('id', $request->service_id)->update(['average_rating' => $avg]);

        return response()->json(['message' => 'Rating added', 'rating' => $rating], 201);
    }
}