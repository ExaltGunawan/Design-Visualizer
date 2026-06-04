<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Batch Add Patterns') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-blue-800 text-sm">
                            <strong>Note:</strong> Patterns will be automatically named based on their file names. 
                            For example, <code>batik_merah.jpg</code> will be saved as <strong>Batik Merah</strong>.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.patterns.batch.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Select Multiple Texture Images</label>
                            <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="files[]" type="file" accept="image/*" multiple required>
                            <p class="text-xs text-gray-500 mt-2">You can select multiple files at once. Recommended: Seamlessly tileable JPG/PNG files.</p>
                        </div>
                        <div class="flex items-center justify-between mt-8">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow" type="submit">
                                Upload & Create Patterns
                            </button>
                            <a href="{{ route('admin.patterns.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
