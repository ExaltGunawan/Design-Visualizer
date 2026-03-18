<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GridPreset;
use Illuminate\Http\Request;

class GridPresetController extends Controller
{
    public function index()
    {
        $gridPresets = GridPreset::all();
        return view('admin.grid-presets.index', compact('gridPresets'));
    }

    public function create()
    {
         return view('admin.grid-presets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'colss' => 'required|integer',
            'rowss' => 'required|integer',
            'scale_value' => 'required|numeric'
        ]);
        
        GridPreset::create($request->all());
        
        return redirect()->route('admin.grid-presets.index')->with('success', 'Preset created successfully.');
    }

    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}

    public function destroy(string $id)
    {
        GridPreset::findOrFail($id)->delete();
        return redirect()->route('admin.grid-presets.index')->with('success', 'Preset deleted successfully.');
    }
}
