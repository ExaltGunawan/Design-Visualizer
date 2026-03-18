<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Pattern') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.patterns.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pattern Name</label>
                            <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="name" type="text" placeholder="e.g. Batik Megamendung" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Texture Image (Seamless)</label>
                            <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="file_path" type="file" accept="image/*" required>
                            <p class="text-xs text-gray-500 mt-1">Recommended: JPG/PNG, seamlessly tileable texture.</p>
                        </div>
                        <div class="flex items-center justify-between mt-6">
                            <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded" type="submit">Save Pattern</button>
                            <a href="{{ route('admin.patterns.index') }}" class="text-blue-500">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
