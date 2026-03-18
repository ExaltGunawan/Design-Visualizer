<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Grid Preset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.grid-presets.store') }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Label</label>
                                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="label" type="text" placeholder="e.g. 2x2 Grid" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Scale Value</label>
                                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="scale_value" type="text" placeholder="e.g. 0.50" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Columns</label>
                                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="colss" type="number" min="1" max="10" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Rows</label>
                                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="rowss" type="number" min="1" max="10" required>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded" type="submit">Save Preset</button>
                            <a href="{{ route('admin.grid-presets.index') }}" class="text-blue-500">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
