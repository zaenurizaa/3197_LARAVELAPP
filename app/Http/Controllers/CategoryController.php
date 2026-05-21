<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     */
    public function index(Request $request)
    {
        
        $search = $request->input('search');
        
        $categories = Category::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     */
    public function edit(int $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     */
    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}