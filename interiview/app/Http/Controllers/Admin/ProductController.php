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
            'base_image' => 'required|image',
            'shadow_overlay' => 'required|image',
            'grid_presets' => 'nullable|array',
            'grid_zones' => 'nullable|string'
        ]);
        $baseImagePath = $request->file('base_image')->store('products/base', 'public');
        $shadowImagePath = $request->file('shadow_overlay')->store('products/shadow', 'public');
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'base_image' => $baseImagePath,
            'shadow_overlay' => $shadowImagePath,
            'grid_zones' => $request->grid_zones ? json_decode($request->grid_zones, true) : null,
        ]);
        $product->gridPresets()->attach($request->grid_presets);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }
    public function show(string $id) {}
    public function edit(string $id)
    {
        $product = Product::with('gridPresets')->findOrFail($id);
        $categories = Category::all();
        $gridPresets = GridPreset::all();
        return view('admin.products.edit', compact('product', 'categories', 'gridPresets'));
    }
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|string|max:100',
            'base_image' => 'nullable|image',
            'shadow_overlay' => 'nullable|image',
            'grid_presets' => 'nullable|array',
            'grid_zones' => 'nullable|string'
        ]);
        $baseImagePath = $product->base_image;
        if ($request->hasFile('base_image')) {
            if (\Storage::disk('public')->exists($product->base_image)) {
                \Storage::disk('public')->delete($product->base_image);
            }
            $baseImagePath = $request->file('base_image')->store('products/base', 'public');
        }
        $shadowImagePath = $product->shadow_overlay;
        if ($request->hasFile('shadow_overlay')) {
            if (\Storage::disk('public')->exists($product->shadow_overlay)) {
                \Storage::disk('public')->delete($product->shadow_overlay);
            }
            $shadowImagePath = $request->file('shadow_overlay')->store('products/shadow', 'public');
        }
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'base_image' => $baseImagePath,
            'shadow_overlay' => $shadowImagePath,
            'grid_zones' => $request->grid_zones ? json_decode($request->grid_zones, true) : null,
        ]);
        $product->gridPresets()->sync($request->grid_presets);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        if (\Storage::disk('public')->exists($product->base_image)) {
            \Storage::disk('public')->delete($product->base_image);
        }
        if (\Storage::disk('public')->exists($product->shadow_overlay)) {
            \Storage::disk('public')->delete($product->shadow_overlay);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
