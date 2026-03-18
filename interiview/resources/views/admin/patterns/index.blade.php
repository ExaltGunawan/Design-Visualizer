<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Patterns') }}
            </h2>
            <a href="{{ route('admin.patterns.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Pattern
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
                                <th class="px-6 py-3 text-left">Texture</th>
                                <th class="px-6 py-3 text-left">Pattern Name</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($patterns as $pattern)
                            <tr>
                                <td class="px-6 py-4">
                                    <img src="{{ asset('storage/' . $pattern->file_path) }}" alt="{{ $pattern->name }}" class="h-16 w-16 object-cover rounded shadow">
                                </td>
                                <td class="px-6 py-4 font-semibold">{{ $pattern->name }}</td>
                                <td class="px-6 py-4 text-right flex justify-end">
                                    <form action="{{ route('admin.patterns.destroy', $pattern->id) }}" method="POST" onsubmit="return confirm('Delete this pattern?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
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
