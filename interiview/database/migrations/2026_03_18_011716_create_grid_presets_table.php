<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_presets', function (Blueprint $table) {
            $table->id();
            $table->string('label', 50);
            $table->integer('colss')->default(1);
            $table->integer('rowss')->default(1);
            $table->decimal('scale_value', 5, 2);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('grid_presets');
    }
};
