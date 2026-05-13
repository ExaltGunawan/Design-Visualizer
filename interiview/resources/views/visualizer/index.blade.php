<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InteriView Visualizer</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Mobile Drag & Drop Polyfill -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mobile-drag-drop@3.0.0-beta.0/default.css">
    <script src="https://cdn.jsdelivr.net/npm/mobile-drag-drop@3.0.0-beta.0/index.min.js"></script>
    <script>
        MobileDragDrop.polyfill({
            holdToDrag: 300, // wait 300ms before starting drag to distinct from scrolling
        });
        window.addEventListener('touchmove', function() {}, {passive: false});
    </script>
    <style>
        .dynamic-pill-pos {
            bottom: 1rem; left: 0; right: 0;
        }
        @media (min-width: 1024px) {
            .dynamic-pill-pos { left: 320px; }
        }
        /* Mobile usability: avoid scrolling when dragging */
        .draggable-pattern { touch-action: none; }

        /* Pure CSS Responsive Layout (bypassing Tailwind cache) */
        @media (max-width: 1023px) {
            .mobile-flex-col-reverse {
                flex-direction: column-reverse !important;
                height: auto !important;
                min-height: 100vh !important;
                overflow: visible !important;
            }
            .mobile-sidebar-full {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                border-top: 1px solid #e5e7eb;
                box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
            }
            .mobile-main-auto {
                min-height: 50vh !important;
                height: auto !important;
                overflow: visible !important;
            }
            .mobile-header-stack {
                flex-direction: column !important;
            }
            .mobile-header-btns {
                width: 100% !important;
                justify-content: center !important;
                margin-top: 10px;
                margin-left: 0 !important;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-200">
    <div x-data="visualizerApp()" x-init="initApp()" class="flex h-screen overflow-hidden mobile-flex-col-reverse">
        
        <!-- Sidebar -->
        <aside class="w-80 min-w-[320px] max-w-[320px] bg-white shadow-xl flex flex-col z-10 shrink-0 mobile-sidebar-full">
            <div class="p-6 flex-1 overflow-y-auto space-y-8">
                <!-- 1. Select Product -->
                <div>
                    <h2 class="text-sm font-semibold mb-3">1. Select Product</h2>
                    <select x-model="selectedProductId" @change="onProductChange()" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                        <option value="">Select a product...</option>
                        <template x-for="product in products" :key="product.id">
                            <option :value="product.id" x-text="product.name"></option>
                        </template>
                    </select>
                </div>

                <!-- 2. Select Grid Layout -->
                <div x-show="selectedProduct">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-gray-800">2. Select Layout</h2>
                    </div>
                    <select x-model="selectedGridPresetId" @change="onGridChange()" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-sm">
                        <option value="" x-text="selectedProduct && selectedProduct.grid_zones && selectedProduct.grid_zones.length > 0 ? 'Default: Custom Mapping' : 'Default: No Grid (Single)'"></option>
                        <template x-for="preset in availableGridPresets" :key="preset.id">
                            <option :value="preset.id" x-text="'Grid Layout: ' + preset.label"></option>
                        </template>
                    </select>
                </div>

                <!-- 3. Patterns -->
                <div x-show="selectedProduct">
                    <h2 class="text-sm font-semibold mb-3">3. Patterns</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="pattern in patterns" :key="pattern.id">
                            <div class="draggable-pattern cursor-grab hover:scale-105 transition-all p-1 rounded-md" 
                                :class="activePattern && activePattern.id === pattern.id ? 'bg-blue-100 ring-2 ring-blue-500 shadow-md' : 'hover:bg-gray-100'"
                                draggable="true" 
                                @click="selectPattern(pattern)"
                                @dragstart="dragStart($event, pattern)">
                                <p class="text-xs text-center mb-1 truncate font-medium" :class="activePattern && activePattern.id === pattern.id ? 'text-blue-700' : 'text-gray-700'" x-text="pattern.name"></p>
                                <img :src="'/storage/' + pattern.file_path" :alt="pattern.name" class="w-full h-24 object-cover border border-gray-200 rounded shadow-sm">
                            </div>
                        </template>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-3 text-center bg-gray-100 p-2 rounded">
                        You can <span class="text-blue-600 font-semibold">drag patterns</span>, or <span class="text-blue-600 font-semibold">click to select one</span> and then tap the grid to apply it.
                    </p>
                </div>
            </div>
        </aside>

        <!-- Main Content (Canvas Area placed logically at 'top' of mobile screen due to flex-col-reverse) -->
        <main class="flex-1 flex flex-col relative w-full bg-gray-200 h-screen overflow-hidden mobile-main-auto">
            <!-- Normal Block Header -->
            <header class="w-full bg-white shadow-sm border-b border-gray-200 p-4 flex justify-between items-center shrink-0 z-30 mobile-header-stack">
                <!-- Branding / Title (Left) -->
                <h1 class="text-xl sm:text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 whitespace-nowrap">InteriView Demo</h1>

                <!-- Action Buttons (Right) -->
                <div class="flex items-center gap-3 ml-auto mobile-header-btns">
                    <button @click="resetDesign()" class="px-4 py-2 bg-white text-gray-700 shadow-sm rounded font-medium hover:bg-gray-50 transition border border-gray-300 text-sm cursor-pointer whitespace-nowrap">
                        Reset Design
                    </button>
                    <button @click="downloadDesign()" class="px-4 py-2 bg-blue-600 text-white shadow-sm rounded font-medium hover:bg-blue-700 transition text-sm cursor-pointer whitespace-nowrap">
                        Download
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm ml-2 font-medium text-gray-600 hover:text-gray-900 border-l border-gray-300 pl-4 cursor-pointer">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm ml-2 font-medium text-gray-600 hover:text-gray-900 border-l border-gray-300 pl-4 cursor-pointer">Log in</a>
                    @endauth
                </div>
            </header>

            <!-- Canvas Area -->
            <div class="flex-1 flex items-center justify-center relative p-8 touch-none overflow-hidden" style="touch-action: none;">
                <div class="relative bg-white shadow rounded max-w-full max-h-full flex items-center justify-center p-4">
                    
                    <div x-show="!selectedProduct" class="text-gray-400 text-lg flex flex-col items-center p-12 text-center">
                        <svg class="h-16 w-16 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-lg">Please select a product below</span>
                    </div>

                    <!-- Hidden actual Images for Canvas drawing -->
                    <img x-ref="baseImage" class="hidden" @load="handleImageLoad()">
                    <img x-ref="shadowOverlay" class="hidden" @load="handleImageLoad()">

                    <!-- The interactive canvas container tightly wrapping the canvas -->
                    <div x-show="selectedProduct" class="relative w-full max-w-5xl h-[50vh] lg:h-[70vh] flex items-center justify-center mx-auto" :style="`aspect-ratio: ${imageAspectRatio};`" x-ref="canvasContainer">
                        <!-- Main Canvas -->
                        <canvas x-ref="mainCanvas" class="max-w-full max-h-full object-contain pointer-events-none drop-shadow-md rounded"></canvas>
                        
                        <!-- Grid Overlay (Interactive drop zones) -->
                        <div class="absolute inset-0 grid" 
                             x-show="(selectedGridPresetId || !selectedProduct || !selectedProduct.grid_zones || selectedProduct.grid_zones.length === 0) && (gridConfig.rows > 1 || gridConfig.cols > 1)"
                             :style="`grid-template-rows: repeat(${gridConfig.rows}, 1fr); grid-template-columns: repeat(${gridConfig.cols}, 1fr); z-index: 50;` ">
                             <template x-for="(r, rIndex) in Array.from({length: gridConfig.rows})" :key="'r'+rIndex">
                                <template x-for="(c, cIndex) in Array.from({length: gridConfig.cols})" :key="'c'+cIndex">
                                    <div class="border border-dashed border-blue-500/40 hover:bg-blue-500/20 transition-all cursor-pointer"
                                         @click="applyActivePattern(rIndex, cIndex)"
                                         @dragover.prevent=""
                                         @drop="dropPattern($event, rIndex, cIndex)">
                                    </div>
                                </template>
                             </template>
                        </div>

                        <!-- Single overlay drop zone (when no grid selected and no zones) -->
                        <div class="absolute inset-0 hover:bg-blue-500/10 transition-all border border-dashed border-transparent hover:border-blue-500/40 cursor-pointer" 
                             x-show="(selectedGridPresetId || !selectedProduct || !selectedProduct.grid_zones || selectedProduct.grid_zones.length === 0) && gridConfig.rows === 1 && gridConfig.cols === 1"
                             style="z-index: 50;"
                             @click="applyActivePattern(0, 0)"
                             @dragover.prevent=""
                             @drop="dropPattern($event, 0, 0)">
                        </div>

                        <!-- CUSTOM ZONES OVERLAY -->
                        <template x-if="!selectedGridPresetId && selectedProduct && selectedProduct.grid_zones && selectedProduct.grid_zones.length > 0">
                            <div class="absolute inset-0" style="z-index: 55;">
                                <template x-for="(zone, zIndex) in selectedProduct.grid_zones" :key="zIndex">
                                    <div class="absolute border border-dashed border-red-500/40 hover:bg-red-500/20 transition-all cursor-pointer"
                                         :style="`left: ${zone.x}%; top: ${zone.y}%; width: ${zone.w}%; height: ${zone.h}%;`"
                                         @click="applyActivePattern(zIndex)"
                                         @dragover.prevent=""
                                         @drop="dropPattern($event, zIndex)">
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                </div>
            </div>

            <!-- Footer indicator -->
            <div x-show="selectedProduct" class="fixed flex justify-center pointer-events-none z-50 dynamic-pill-pos">
                <div class="bg-blue-50/90 backdrop-blur px-5 py-2.5 rounded-full text-xs text-blue-700 shadow-md border border-blue-200 font-medium tracking-wide">
                    ✦ Drag a pattern or click a selected pattern to apply to <span x-text="selectedProduct && selectedProduct.grid_zones && selectedProduct.grid_zones.length > 0 ? 'selected zone' : 'grid cell'"></span>.
                </div>
            </div>
        </main>
    </div>

    <!-- Application Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('visualizerApp', () => ({
                products: [],
                patterns: [],
                
                selectedProductId: '',
                selectedProduct: null,
                
                availableGridPresets: [],
                selectedGridPresetId: '',
                
                // Active Latch / Selected Pattern Tool
                activePattern: null,

                // Fix for 3D inverted Y-axis renders
                reverseY: true,

                // Grid matrix state to hold pattern URLs
                gridConfig: { rows: 1, cols: 1 },
                gridData: [], // 2D array OR 1D array (for zones)
                
                imageAspectRatio: '1 / 1',

                async initApp() {
                    try {
                        const [prodRes, patRes] = await Promise.all([
                            fetch('/api/products'),
                            fetch('/api/patterns')
                        ]);
                        this.products = await prodRes.json();
                        this.patterns = await patRes.json();
                    } catch (error) {
                        console.error("Failed to load generic data", error);
                    }

                    // Window resize listener to redraw canvas if necessary
                    window.addEventListener('resize', () => {
                        if(this.selectedProduct) {
                            this.drawCanvas(); // Debounce this in production
                        }
                    });
                },

                handleImageLoad() {
                    const width = this.$refs.baseImage.naturalWidth || 800;
                    const height = this.$refs.baseImage.naturalHeight || 800;
                    this.imageAspectRatio = `${width} / ${height}`;
                    this.drawCanvas();
                },
                
                onProductChange() {
                    const id = parseInt(this.selectedProductId);
                    this.selectedProduct = this.products.find(p => p.id === id) || null;
                    this.activePattern = null; 
                    
                    if (this.selectedProduct) { 
                        this.availableGridPresets = this.selectedProduct.grid_presets || [];
                        this.selectedGridPresetId = ''; 
                        
                        this.$refs.baseImage.src = '/storage/' + this.selectedProduct.base_image;
                        this.$refs.shadowOverlay.src = '/storage/' + this.selectedProduct.shadow_overlay;
                        
                        // Initialize grid correctly
                        this.onGridChange(); 
                    } else {
                        const canvas = this.$refs.mainCanvas;
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                },
                
                onGridChange() {
                    // CASE A: Custom Zones Mode (No Grid Preset selected AND Zones exist)
                    if (!this.selectedGridPresetId && this.selectedProduct && this.selectedProduct.grid_zones && this.selectedProduct.grid_zones.length > 0) {
                        this.gridData = Array(this.selectedProduct.grid_zones.length).fill(null);
                        this.drawCanvas();
                        return;
                    }

                    // CASE B: Standard Grid Mode
                    let preset = null;
                    if (this.selectedGridPresetId) {
                        const presetId = parseInt(this.selectedGridPresetId);
                        preset = this.availableGridPresets.find(p => p.id === presetId);
                    }
                    
                    if (preset) {
                        const dimensionMatch = preset.label.match(/(\d+)\s*x\s*(\d+)/i); 
                        if (dimensionMatch) {
                            this.gridConfig = { 
                                cols: parseInt(dimensionMatch[1]), 
                                rows: parseInt(dimensionMatch[2]) 
                            };
                        } else {
                            this.gridConfig = { rows: 1, cols: 1 };
                        }
                    } else {
                        this.gridConfig = { rows: 1, cols: 1 };
                    }
                    
                    this.gridData = Array(this.gridConfig.rows).fill(null).map(() => Array(this.gridConfig.cols).fill(null));
                    this.drawCanvas();
                },

                toggleReverseY() {
                    this.reverseY = !this.reverseY;
                    this.resetDesign();
                },
                
                selectPattern(pattern) {
                    if (this.activePattern && this.activePattern.id === pattern.id) {
                        this.activePattern = null;
                    } else {
                        this.activePattern = pattern;
                    }
                },

                applyActivePattern(arg1, arg2) {
                    if (!this.activePattern) return;
                    
                    if (this.selectedProduct && this.selectedProduct.grid_zones && this.selectedProduct.grid_zones.length > 0 && !this.selectedGridPresetId) {
                        // Custom Zones Mode
                        this.gridData[arg1] = '/storage/' + this.activePattern.file_path;
                    } else {
                        // arg1=row, arg2=col
                        const mappedRow = this.reverseY ? (this.gridConfig.rows - 1 - arg1) : arg1;
                        this.gridData[mappedRow][arg2] = '/storage/' + this.activePattern.file_path;
                    }
                    this.drawCanvas();
                },
                
                dragStart(event, pattern) {
                    this.activePattern = pattern;
                    event.dataTransfer.setData('text/plain', JSON.stringify(pattern));
                    event.dataTransfer.effectAllowed = 'copy';
                },
                
                dropPattern(event, arg1, arg2) {
                    const dataStr = event.dataTransfer.getData('text/plain');
                    if (!dataStr) return;
                    
                    try {
                        const pattern = JSON.parse(dataStr);
                        const pUrl = '/storage/' + pattern.file_path;

                        if (this.selectedProduct && this.selectedProduct.grid_zones && this.selectedProduct.grid_zones.length > 0 && !this.selectedGridPresetId) {
                            // Custom Zones Mode
                            this.gridData[arg1] = pUrl;
                        } else {
                            // arg1=row, arg2=col
                            const mappedRow = this.reverseY ? (this.gridConfig.rows - 1 - arg1) : arg1;
                            this.gridData[mappedRow][arg2] = pUrl;
                        }
                        this.drawCanvas();
                    } catch (e) {
                        console.error('Invalid drop data');
                    }
                },
                
                resetDesign() {
                    if (this.selectedProduct && this.selectedProduct.grid_zones && this.selectedProduct.grid_zones.length > 0 && !this.selectedGridPresetId) {
                        this.gridData = Array(this.selectedProduct.grid_zones.length).fill(null);
                    } else {
                        this.gridData = Array(this.gridConfig.rows).fill(null).map(() => Array(this.gridConfig.cols).fill(null));
                    }
                    this.drawCanvas();
                },
                
                downloadDesign() {
                    const canvas = this.$refs.mainCanvas;
                    const link = document.createElement('a');
                    link.download = `interiview-design-${new Date().getTime()}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                },
                
                async drawCanvas() {
                    if (!this.selectedProduct || !this.$refs.baseImage.complete || !this.$refs.shadowOverlay.complete) {
                        return; 
                    }
                    
                    const canvas = this.$refs.mainCanvas;
                    const ctx = canvas.getContext('2d');
                    
                    const baseRes = 1024;
                    const imgWidth = this.$refs.baseImage.naturalWidth || 800;
                    const imgHeight = this.$refs.baseImage.naturalHeight || 800;
                    
                    const scale = Math.min(baseRes / imgWidth, baseRes / imgHeight);
                    canvas.width = imgWidth * scale;
                    canvas.height = imgHeight * scale;
                    
                    const drawWidth = canvas.width;
                    const drawHeight = canvas.height;
                    
                    const patternCanvas = document.createElement('canvas');
                    patternCanvas.width = drawWidth;
                    patternCanvas.height = drawHeight;
                    const ptx = patternCanvas.getContext('2d');
                    
                    const drawPromises = [];

                    // CASE A: Custom Zones (Only if no Grid Preset is selected)
                    if (this.selectedProduct.grid_zones && this.selectedProduct.grid_zones.length > 0 && !this.selectedGridPresetId) {
                        this.selectedProduct.grid_zones.forEach((zone, index) => {
                            const pUrl = this.gridData[index];
                            if (pUrl) {
                                drawPromises.push(new Promise((resolve) => {
                                    const img = new Image();
                                    img.onload = () => {
                                        const zX = (zone.x / 100) * drawWidth;
                                        const zY = (zone.y / 100) * drawHeight;
                                        const zW = (zone.w / 100) * drawWidth;
                                        const zH = (zone.h / 100) * drawHeight;

                                        ptx.save();
                                        ptx.beginPath();
                                        ptx.rect(zX, zY, zW, zH);
                                        ptx.clip();
                                        
                                        const pattern = ptx.createPattern(img, 'repeat');
                                        const targetSize = drawWidth / 3.5; 
                                        const scaleFactor = targetSize / img.width;
                                        const domMatrix = new DOMMatrix().scale(scaleFactor, scaleFactor);
                                        pattern.setTransform(domMatrix);
                                        
                                        ptx.fillStyle = pattern;
                                        ptx.fillRect(zX, zY, zW, zH);
                                        ptx.restore();
                                        resolve();
                                    };
                                    img.onerror = resolve;
                                    img.src = pUrl;
                                }));
                            }
                        });
                    } 
                    // CASE B: Standard Grid
                    else {
                        const cellWidth = drawWidth / this.gridConfig.cols;
                        const cellHeight = drawHeight / this.gridConfig.rows;
                        
                        for(let r=0; r<this.gridConfig.rows; r++) {
                            for(let c=0; c<this.gridConfig.cols; c++) {
                                const pUrl = this.gridData[r][c];
                                if (pUrl) {
                                    drawPromises.push(new Promise((resolve) => {
                                        const img = new Image();
                                        img.onload = () => {
                                            ptx.save();
                                            ptx.beginPath();
                                            ptx.rect(c*cellWidth, r*cellHeight, cellWidth, cellHeight);
                                            ptx.clip();
                                            
                                            const pattern = ptx.createPattern(img, 'repeat');
                                            const targetSize = drawWidth / 3.5; 
                                            const scaleFactor = targetSize / img.width;
                                            const domMatrix = new DOMMatrix().scale(scaleFactor, scaleFactor);
                                            pattern.setTransform(domMatrix);
                                            
                                            ptx.fillStyle = pattern;
                                            ptx.fillRect(c*cellWidth, r*cellHeight, cellWidth, cellHeight);
                                            ptx.restore();
                                            resolve();
                                        };
                                        img.onerror = resolve;
                                        img.src = pUrl;
                                    }));
                                }
                            }
                        }
                    }
                    
                    await Promise.all(drawPromises);
                    
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(patternCanvas, 0, 0);
                    ctx.globalCompositeOperation = 'destination-in';
                    ctx.drawImage(this.$refs.baseImage, 0, 0, canvas.width, canvas.height);
                    ctx.globalCompositeOperation = 'multiply';
                    ctx.drawImage(this.$refs.shadowOverlay, 0, 0, canvas.width, canvas.height);
                    ctx.globalCompositeOperation = 'source-over';
                    ctx.globalCompositeOperation = 'destination-over';
                    ctx.drawImage(this.$refs.baseImage, 0, 0, canvas.width, canvas.height);
                    ctx.globalCompositeOperation = 'source-over';
                }
            }));
        });
    </script>
</body>
</html>
