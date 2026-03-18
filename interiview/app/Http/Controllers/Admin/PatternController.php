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
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}

    public function destroy(string $id)
    {
        Pattern::findOrFail($id)->delete();
        return redirect()->route('admin.patterns.index')->with('success', 'Pattern deleted successfully.');
    }
}
