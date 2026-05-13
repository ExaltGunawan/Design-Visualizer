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
                            <div class="col-span-2 border-t pt-6 mt-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Grid Layout Presets (Auto-Grid)</label>
                                <p class="text-xs text-gray-500 mb-3">Select standard grid layouts that will be available for this product. (Uniform boxes).</p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50 p-4 rounded border">
                                    @foreach($gridPresets as $preset)
                                        <label class="flex items-center space-x-3 p-2 hover:bg-white rounded transition-colors cursor-pointer border border-transparent hover:border-gray-200">
                                            <input type="checkbox" name="grid_presets[]" value="{{ $preset->id }}" 
                                                {{ $product->gridPresets->contains($preset->id) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm text-gray-700">{{ $preset->label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-span-2 border-t pt-6 mt-4">
                                <label class="block text-gray-700 text-sm font-bold mb-4 italic">Custom Grid Zones (Manual Mapping)</label>
                                <p class="text-xs text-gray-500 mb-4"><strong>Option B:</strong> If you want to define specific areas (e.g., individual panels of a headboard or separate sofas), drag boxes on the image below.</p>
                                
                                <div x-data="zoneMapper(@js($product->grid_zones ?? []))" class="space-y-4">
                                    <input type="hidden" name="grid_zones" :value="JSON.stringify(zones)">
                                    
                                    <div class="flex flex-col lg:flex-row gap-6">
                                        <!-- Interactive Canvas -->
                                        <div class="relative bg-gray-200 border-2 border-gray-300 rounded shadow-inner overflow-hidden select-none cursor-crosshair" 
                                             x-ref="mapperContainer"
                                             style="width: 100%; max-width: 800px; min-height: 300px;"
                                             @mousedown.prevent="startDrawing($event)">
                                            
                                            <!-- Real Image Background (Guide) -->
                                            <img src="{{ Storage::url($product->shadow_overlay) }}" 
                                                 @load="adjustHeight()"
                                                 x-ref="mapperImage"
                                                 draggable="false"
                                                 class="block w-full h-auto pointer-events-none select-none"
                                                 style="z-index: 10;">

                                            <!-- Mask Area Indicator (White Ghost) -->
                                            <img src="{{ Storage::url($product->base_image) }}" 
                                                 class="absolute inset-0 w-full h-full object-contain pointer-events-none select-none"
                                                 style="z-index: 15; opacity: 0.8;">
                                            
                                            <!-- Existing Zones -->
                                            <template x-for="(zone, index) in zones" :key="index">
                                                <div class="absolute flex items-center justify-center group"
                                                     :style="`left: ${zone.x}%; top: ${zone.y}%; width: ${zone.w}%; height: ${zone.h}%; z-index: 100; border: 2.5px solid #ef4444; background-color: rgba(239, 68, 68, 0.35); box-shadow: 0 0 8px rgba(0,0,0,0.2);`"
                                                     @mousedown.stop="">
                                                    <div style="background-color: #b91c1c; color: white; font-size: 11px; font-weight: bold; padding: 2px 6px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.4);" x-text="index + 1"></div>
                                                    <button type="button" @click="removeZone(index)" 
                                                            style="position: absolute; top: -12px; right: -12px; background-color: #b91c1c; color: white; border-radius: 999px; width: 24px; height: 24px; display: none; align-items: center; justify-content: center; font-size: 16px; font-weight: bold; border: 2px solid white; cursor: pointer; z-index: 110;"
                                                            class="group-hover:flex">×</button>
                                                </div>
                                            </template>
                                            
                                            <!-- Current Drawing Box (while dragging) -->
                                            <div x-show="isDrawing" 
                                                 :style="`position: absolute; left: ${currentBox.x}%; top: ${currentBox.y}%; width: ${currentBox.w}%; height: ${currentBox.h}%; border: 3px solid #facc15; background-color: rgba(250, 204, 21, 0.3); z-index: 150; pointer-events: none; outline: 1px solid black; box-shadow: 0 0 10px rgba(0,0,0,0.5); display: block;` ">
                                            </div>
                                        </div>

                                        <!-- Zones List -->
                                        <div class="w-full lg:w-64">
                                            <h3 class="text-xs font-bold uppercase text-gray-400 mb-2">Mapped Zones</h3>
                                            <div class="space-y-2 max-h-[450px] overflow-y-auto pr-2">
                                                <template x-if="zones.length === 0">
                                                    <p class="text-sm text-gray-400 italic bg-gray-50 p-4 border rounded border-dashed">No zones defined yet. Drag on the image to create one.</p>
                                                </template>
                                                <template x-for="(zone, index) in zones" :key="index">
                                                    <div class="flex items-center justify-between p-3 bg-white border-l-4 border-l-red-500 shadow-sm rounded text-xs hover:bg-red-50 transition-colors">
                                                        <span class="font-medium">Zone <span x-text="index + 1"></span> (<span x-text="Math.round(zone.w)"></span>% x <span x-text="Math.round(zone.h)"></span>%)</span>
                                                        <button type="button" @click="removeZone(index)" class="text-red-600 font-bold hover:text-red-800">Delete</button>
                                                    </div>
                                                </template>
                                            </div>
                                            <button type="button" x-show="zones.length > 0" @click="zones = []" class="mt-4 w-full py-2 border border-red-200 text-xs text-red-600 font-bold rounded hover:bg-red-50 transition-colors">Clear All Zones</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            function zoneMapper(initialZones) {
                                return {
                                    zones: initialZones,
                                    isDrawing: false,
                                    startX: 0,
                                    startY: 0,
                                    currentBox: { x: 0, y: 0, w: 0, h: 0 },

                                    adjustHeight() {
                                        // Image will define the height automatically
                                    },

                                    startDrawing(e) {
                                        const rect = this.$refs.mapperContainer.getBoundingClientRect();
                                        this.isDrawing = true;
                                        this.startX = ((e.clientX - rect.left) / rect.width) * 100;
                                        this.startY = ((e.clientY - rect.top) / rect.height) * 100;
                                        this.currentBox = { x: this.startX, y: this.startY, w: 0, h: 0 };

                                        // Attach temporary global listeners
                                        this._onMove = (moveEvent) => this.draw(moveEvent);
                                        this._onUp = () => this.stopDrawing();
                                        
                                        window.addEventListener('mousemove', this._onMove);
                                        window.addEventListener('mouseup', this._onUp);
                                    },

                                    draw(e) {
                                        if (!this.isDrawing) return;
                                        const rect = this.$refs.mapperContainer.getBoundingClientRect();
                                        
                                        // Clamp values between 0 and 100%
                                        let curX = ((e.clientX - rect.left) / rect.width) * 100;
                                        let curY = ((e.clientY - rect.top) / rect.height) * 100;
                                        
                                        curX = Math.max(0, Math.min(100, curX));
                                        curY = Math.max(0, Math.min(100, curY));
                                        
                                        // Update object by replacement to trigger reactivity
                                        this.currentBox = {
                                            x: Math.min(this.startX, curX),
                                            y: Math.min(this.startY, curY),
                                            w: Math.abs(curX - this.startX),
                                            h: Math.abs(curY - this.startY)
                                        };
                                    },

                                    stopDrawing() {
                                        if (!this.isDrawing) return;
                                        
                                        window.removeEventListener('mousemove', this._onMove);
                                        window.removeEventListener('mouseup', this._onUp);

                                        if (this.currentBox.w > 0.5 && this.currentBox.h > 0.5) {
                                            this.zones.push({...this.currentBox});
                                        }
                                        this.isDrawing = false;
                                        this.currentBox = { x: 0, y: 0, w: 0, h: 0 };
                                    },

                                    removeZone(index) {
                                        this.zones.splice(index, 1);
                                    }
                                }
                            }
                        </script>
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
