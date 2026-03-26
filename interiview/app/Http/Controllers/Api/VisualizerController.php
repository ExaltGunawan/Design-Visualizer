<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pattern;
use App\Models\Product;
use Illuminate\Http\Request;

class VisualizerController extends Controller
{
    public function getProducts()
    {
        // Load products with their allowed grid presets
        $products = Product::with(['category', 'gridPresets'])->get();
        return response()->json($products);
    }

    public function getPatterns()
    {
        $patterns = Pattern::all();
        return response()->json($patterns);
    }
}
