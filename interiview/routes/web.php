<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisualizerController;
use App\Http\Controllers\HomeController;
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/products', [VisualizerController::class, 'getProducts']);
Route::get('/api/patterns', [VisualizerController::class, 'getPatterns']);
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GridPresetController;
use App\Http\Controllers\Admin\PatternController;
use App\Http\Controllers\Admin\ProductController;
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::get('patterns/batch', [PatternController::class, 'batchCreate'])->name('patterns.batch');
    Route::post('patterns/batch', [PatternController::class, 'batchStore'])->name('patterns.batch.store');
    Route::resource('patterns', PatternController::class);
    Route::resource('grid-presets', GridPresetController::class);
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
