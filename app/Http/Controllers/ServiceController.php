<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    // Semua service
    public function index()
    {
        return response()->json(Service::with(['user','category','ratings'])->get());
    }

    // Tambah service baru
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'service_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
        ]);

        $service = Service::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'service_name' => $request->service_name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service
        ], 201);
    }

    // Detail service
    public function show($id)
    {
        $service = Service::with(['user','category','ratings'])->findOrFail($id);
        return response()->json($service);
    }

    // Update service
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'service_name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'nullable|numeric',
        ]);

        $service = Service::findOrFail($id);
        $service->update($request->only('category_id', 'service_name', 'description', 'price'));

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service
        ]);
    }

    // Hapus service
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}