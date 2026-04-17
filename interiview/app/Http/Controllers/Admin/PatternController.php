<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pattern;
use Illuminate\Http\Request;

class PatternController extends Controller
{
    public function index()
    {
        $patterns = Pattern::all();
        return view('admin.patterns.index', compact('patterns'));
    }

    public function create()
    {
        return view('admin.patterns.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'file_path' => 'required|image|mimes:jpeg,png,jpg'
        ]);

        $path = $request->file('file_path')->store('patterns', 'public');

        Pattern::create([
            'name' => $request->name,
            'file_path' => $path,
        ]);

        return redirect()->route('admin.patterns.index')->with('success', 'Pattern created successfully.');
    }

    public function show(string $id) {}

    public function edit(string $id)
    {
        $pattern = Pattern::findOrFail($id);
        return view('admin.patterns.edit', compact('pattern'));
    }

    public function update(Request $request, string $id)
    {
        $pattern = Pattern::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'file_path' => 'nullable|image|mimes:jpeg,png,jpg'
        ]);

        $path = $pattern->file_path;
        if ($request->hasFile('file_path')) {
            if (\Storage::disk('public')->exists($pattern->file_path)) {
                \Storage::disk('public')->delete($pattern->file_path);
            }
            $path = $request->file('file_path')->store('patterns', 'public');
        }

        $pattern->update([
            'name' => $request->name,
            'file_path' => $path,
        ]);

        return redirect()->route('admin.patterns.index')->with('success', 'Pattern updated successfully.');
    }

    public function destroy(string $id)
    {
        $pattern = Pattern::findOrFail($id);
        if (\Storage::disk('public')->exists($pattern->file_path)) {
            \Storage::disk('public')->delete($pattern->file_path);
        }
        $pattern->delete();

        return redirect()->route('admin.patterns.index')->with('success', 'Pattern deleted successfully.');
    }
}
