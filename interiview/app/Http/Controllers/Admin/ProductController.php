<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\GridPreset;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $gridPresets = GridPreset::all();
        return view('admin.products.create', compact('categories', 'gridPresets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|string|max:100',
            'base_image' => 'required|image|mimes:png',
            'shadow_overlay' => 'required|image|mimes:png',
            'grid_presets' => 'required|array'
        ]);

        $baseImagePath = $request->file('base_image')->store('products/base', 'public');
        $shadowImagePath = $request->file('shadow_overlay')->store('products/shadow', 'public');

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'base_image' => $baseImagePath,
            'shadow_overlay' => $shadowImagePath,
        ]);

        $product->gridPresets()->attach($request->grid_presets);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}

    public function destroy(string $id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
