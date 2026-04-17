<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @if ($errors->any())
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong>Whoops! Something went wrong.</strong>
                                <ul class="mt-2 list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="name" type="text" value="{{ old('name', $product->name) }}" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                                <select class="shadow border rounded w-full py-2 px-3 text-gray-700" name="category_id" required>
                                    <option value="">Select Category...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Base Image (PNG)</label>
                                @if($product->base_image)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($product->base_image) }}" alt="Base Image" class="w-32 h-32 object-cover border rounded">
                                    </div>
                                @endif
                                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="base_image" type="file" accept="image/png">
                                <p class="text-xs text-gray-500 mt-1">Leave empty to keep existing image.</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Shadow Overlay (PNG)</label>
                                @if($product->shadow_overlay)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($product->shadow_overlay) }}" alt="Shadow Overlay" class="w-32 h-32 object-cover border rounded">
                                    </div>
                                @endif
                                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="shadow_overlay" type="file" accept="image/png">
                                <p class="text-xs text-gray-500 mt-1">Leave empty to keep existing image.</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Allowed Grid Presets</label>
                                <p class="text-xs text-gray-500 mb-2">Select the grid options user can choose for this product:</p>
                                <div class="flex flex-wrap gap-4">
                                    @php
                                        $selectedPresets = $product->gridPresets->pluck('id')->toArray();
                                    @endphp
                                    @foreach($gridPresets as $preset)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="grid_presets[]" value="{{ $preset->id }}" class="form-checkbox h-5 w-5 text-blue-600" {{ in_array($preset->id, $selectedPresets) ? 'checked' : '' }}>
                                            <span class="ml-2 text-gray-700">{{ $preset->label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-6">
                            <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded" type="submit">Update Product</button>
                            <a href="{{ route('admin.products.index') }}" class="text-blue-500">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
