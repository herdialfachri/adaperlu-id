<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json(Service::with('category', 'user')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
        ]);

        $service = Service::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return response()->json(['message' => 'Service created', 'service' => $service]);
    }

    public function show($id)
    {
        $service = Service::with('category','user','ratings')->findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->only('category_id','title','description','price'));
        return response()->json(['message' => 'Service updated', 'service' => $service]);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return response()->json(['message' => 'Service deleted']);
    }
}