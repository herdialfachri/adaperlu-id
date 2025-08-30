<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // Menampilkan semua kategori (bisa diakses semua user login)
    public function index()
    {
        return response()->json(Category::with('user')->get());
    }

    // Menambah kategori baru (khusus admin)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'user_id' => Auth::id(), // kategori dibuat oleh user yang login
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    // Menampilkan detail kategori
    public function show($id)
    {
        $category = Category::with('user')->findOrFail($id);
        return response()->json($category);
    }

    // Update kategori (khusus admin)
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->only('name','description'));

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    // Hapus kategori (khusus admin)
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}