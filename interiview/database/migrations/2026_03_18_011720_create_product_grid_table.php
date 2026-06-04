<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_grid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('grid_preset_id')->constrained('grid_presets')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('product_grid');
    }
};
