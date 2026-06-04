<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pattern') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.patterns.update', $pattern->id) }}" enctype="multipart/form-data">
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
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pattern Name</label>
                            <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="name" type="text" value="{{ old('name', $pattern->name) }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Texture Image (Seamless)</label>
                            @if($pattern->file_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($pattern->file_path) }}" alt="Pattern Image" class="w-32 h-32 object-cover border rounded">
                                </div>
                            @endif
                            <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="file_path" type="file" accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">Recommended: JPG/PNG. Leave empty to keep existing texture.</p>
                        </div>
                        <div class="flex items-center justify-between mt-6">
                            <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded" type="submit">Update Pattern</button>
                            <a href="{{ route('admin.patterns.index') }}" class="text-blue-500">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
