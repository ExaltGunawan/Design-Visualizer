<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patterns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('file_path');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('patterns');
    }
};
