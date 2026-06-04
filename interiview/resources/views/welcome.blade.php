<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InteriView Prototype</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <style>
        .pattern-item { cursor: grab; }
        .pattern-item:active { cursor: grabbing; }
    </style>
</head>
<body class="bg-gray-100 h-screen overflow-hidden flex flex-col">
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">InteriView Demo</h1>
        <div class="space-x-4">
            <button id="btn-reset" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Reset Design</button>
            <button id="btn-download" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Download</button>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                @endauth
            @endif
        </div>
    </header>
    <main class="flex-1 flex overflow-hidden">
        <aside class="w-64 bg-white border-r border-gray-200 p-4 flex flex-col overflow-y-auto">
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-2">1. Select Product</h3>
                <select id="product-selector" class="w-full border-gray-300 rounded shadow-sm p-2 bg-gray-50">
                    <option value="">Loading products...</option>
                </select>
            </div>
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-2">2. Select Grid</h3>
                <select id="grid-selector" class="w-full border-gray-300 rounded shadow-sm p-2 bg-gray-50" disabled>
                    <option value="">Select a product first</option>
                </select>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-700 mb-2">3. Patterns (Drag to Grid)</h3>
                <div id="pattern-list" class="grid grid-cols-2 gap-2">
                    <div class="text-sm text-gray-500">Loading...</div>
                </div>
            </div>
        </aside>
        <section class="flex-1 bg-gray-200 flex justify-center items-center relative" id="canvas-container">
            <div class="shadow-lg bg-white relative" style="width: 500px; height: 500px;">
                <canvas id="main-canvas" width="500" height="500"></canvas>
            </div>
            <p class="absolute bottom-4 text-gray-500 bg-white px-3 py-1 rounded shadow text-sm">Drag a pattern and drop it over a grid cell.</p>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let products = [];
            let patterns = [];
            let currentProduct = null;
            let currentGrid = null;
            let draggedPatternUrl = null;
            let gridRects = []; // Store fabric Rect objects for drop detection
            const productSelect = document.getElementById('product-selector');
            const gridSelect = document.getElementById('grid-selector');
            const patternList = document.getElementById('pattern-list');
            const btnReset = document.getElementById('btn-reset');
            const btnDownload = document.getElementById('btn-download');
            const canvas = new fabric.Canvas('main-canvas', {
                preserveObjectStacking: true, // Keep shadow on top
                selection: false // Disable group selection
            });
            let baseImageLayer = null;
            let shadowOverlayLayer = null;
            Promise.all([
                fetch('/api/products').then(res => res.json()),
                fetch('/api/patterns').then(res => res.json())
            ]).then(([productsData, patternsData]) => {
                products = productsData;
                patterns = patternsData;
                renderProducts();
                renderPatterns();
            });
            function renderProducts() {
                productSelect.innerHTML = '<option value="">-- Choose Product --</option>';
                products.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = `${p.category ? p.category.name + ' - ' : ''}${p.name}`;
                    productSelect.appendChild(opt);
                });
            }
            function renderPatterns() {
                patternList.innerHTML = '';
                patterns.forEach(pat => {
                    const img = document.createElement('img');
                    img.src = '/storage/' + pat.file_path;
                    img.alt = pat.name;
                    img.title = pat.name;
                    img.className = 'w-full h-24 object-cover rounded border hover:border-blue-500 pattern-item';
                    img.draggable = true;
                    img.addEventListener('dragstart', (e) => {
                        draggedPatternUrl = img.src;
                        e.dataTransfer.setData('text/plain', pat.id); // Required for Firefox Firefox
                        e.dataTransfer.effectAllowed = 'copy';
                    });
                    patternList.appendChild(img);
                });
            }
            productSelect.addEventListener('change', (e) => {
                const pid = parseInt(e.target.value);
                currentProduct = products.find(p => p.id === pid);
                if (currentProduct) {
                    renderGridOptions();
                    loadProductCanvas();
                } else {
                    canvas.clear();
                    gridSelect.innerHTML = '<option value="">Select a product first</option>';
                    gridSelect.disabled = true;
                }
            });
            function renderGridOptions() {
                gridSelect.innerHTML = '';
                if (currentProduct.grid_presets && currentProduct.grid_presets.length > 0) {
                    currentProduct.grid_presets.forEach((grid, index) => {
                        const opt = document.createElement('option');
                        opt.value = grid.id;
                        opt.textContent = grid.label;
                        gridSelect.appendChild(opt);
                    });
                    gridSelect.disabled = false;
                    currentGrid = currentProduct.grid_presets[0]; // Auto select first
                    drawGridOnCanvas();
                } else {
                    gridSelect.innerHTML = '<option value="">No grids available</option>';
                    gridSelect.disabled = true;
                    currentGrid = null;
                }
            }
            gridSelect.addEventListener('change', (e) => {
                const gid = parseInt(e.target.value);
                currentGrid = currentProduct.grid_presets.find(g => g.id === gid);
                drawGridOnCanvas();
            });
            function loadProductCanvas() {
                canvas.clear();
                gridRects = [];
                const canvasW = canvas.width;
                const canvasH = canvas.height;
                fabric.Image.fromURL('/storage/' + currentProduct.base_image, function(img) {
                    img.set({
                        left: 0,
                        top: 0,
                        selectable: false,
                        evented: false,
                        scaleX: canvasW / img.width,
                        scaleY: canvasH / img.height
                    });
                    baseImageLayer = img;
                    canvas.add(img);
                    img.sendToBack();
                    if(currentGrid) drawGridOnCanvas();
                    fabric.Image.fromURL('/storage/' + currentProduct.shadow_overlay, function(shadow) {
                        shadow.set({
                            left: 0,
                            top: 0,
                            selectable: false,
                            evented: false, // Let clicks pass through to grid cells below
                            globalCompositeOperation: 'multiply', // CRITICAL: Makes it look like realistic shadow
                            scaleX: canvasW / shadow.width,
                            scaleY: canvasH / shadow.height,
                            opacity: 0.8
                        });
                        shadowOverlayLayer = shadow;
                        canvas.add(shadow);
                        shadow.bringToFront();
                    });
                });
            }
            function drawGridOnCanvas() {
                if (!baseImageLayer || !currentGrid) return;
                gridRects.forEach(rect => canvas.remove(rect));
                gridRects = [];
                const cols = currentGrid.colss;
                const rows = currentGrid.rowss;
                const cellW = canvas.width / cols;
                const cellH = canvas.height / rows;
                for (let r = 0; r < rows; r++) {
                    for (let c = 0; c < cols; c++) {
                        const rect = new fabric.Rect({
                            left: c * cellW,
                            top: r * cellH,
                            width: cellW,
                            height: cellH,
                            fill: 'rgba(255, 255, 255, 0.7)', // Initial semi-transparent white
                            stroke: '#ccc',
                            strokeWidth: 1,
                            strokeDashArray: [5, 5],
                            selectable: false,
                            hoverCursor: 'crosshair',
                            isGridCell: true,
                            gridX: c,
                            gridY: r,
                            globalCompositeOperation: 'source-atop'
                        });
                        gridRects.push(rect);
                        canvas.add(rect);
                        if (shadowOverlayLayer) {
                            shadowOverlayLayer.bringToFront();
                        }
                    }
                }
                canvas.requestRenderAll();
            }
            const container = document.getElementById('canvas-container');
            container.addEventListener('dragover', (e) => {
                e.preventDefault(); // allow dropping
                e.dataTransfer.dropEffect = 'copy';
            });
            container.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!draggedPatternUrl) return;
                const rect = canvas.lowerCanvasEl.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                if(!currentGrid) return;
                const cols = currentGrid.colss;
                const rows = currentGrid.rowss;
                const cellW = canvas.width / cols;
                const cellH = canvas.height / rows;
                const targetCol = Math.floor(x / cellW);
                const targetRow = Math.floor(y / cellH);
                if(targetCol >= 0 && targetCol < cols && targetRow >= 0 && targetRow < rows) {
                    const targetRect = gridRects.find(r => r.gridX === targetCol && r.gridY === targetRow);
                    if(targetRect) {
                        applyPatternToCell(targetRect, draggedPatternUrl, currentGrid.scale_value);
                    }
                }
                draggedPatternUrl = null;
            });
            function applyPatternToCell(rect, patternUrl, scaleValue) {
                fabric.util.loadImage(patternUrl, function(img) {
                    const pattern = new fabric.Pattern({
                        source: img,
                        repeat: 'repeat'
                    });
                    pattern.patternTransform = [scaleValue, 0, 0, scaleValue, 0, 0];
                    rect.set('fill', pattern);
                    rect.set('strokeWidth', 0); // remove dashed border
                    canvas.requestRenderAll();
                });
            }
            btnReset.addEventListener('click', () => {
                drawGridOnCanvas(); // Redrawing clears the patterns
            });
            btnDownload.addEventListener('click', () => {
                const dataURL = canvas.toDataURL({
                    format: 'png',
                    quality: 1
                });
                const link = document.createElement('a');
                link.download = 'interiview-design.png';
                link.href = dataURL;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
</body>
</html>
