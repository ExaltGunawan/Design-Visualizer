<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Grid Presets') }}
            </h2>
            <a href="{{ route('admin.grid-presets.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Preset
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Label</th>
                                <th class="px-6 py-3 text-left">Columns</th>
                                <th class="px-6 py-3 text-left">Rows</th>
                                <th class="px-6 py-3 text-left">Scale</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($gridPresets as $preset)
                            <tr>
                                <td class="px-6 py-4">{{ $preset->label }}</td>
                                <td class="px-6 py-4">{{ $preset->colss }}</td>
                                <td class="px-6 py-4">{{ $preset->rowss }}</td>
                                <td class="px-6 py-4">{{ $preset->scale_value }}</td>
                                <td class="px-6 py-4 text-right flex justify-end">
                                    <form action="{{ route('admin.grid-presets.destroy', $preset->id) }}" method="POST" onsubmit="return confirm('Delete this preset?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
