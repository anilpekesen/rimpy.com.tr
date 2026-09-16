<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key', 60)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('image_id')->nullable();
            $table->json('eyebrow')->nullable();
            $table->json('title')->nullable();
            $table->json('text')->nullable();
            $table->json('button_label')->nullable();
            $table->json('link')->nullable();
            $table->string('align', 12)->default('left');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
        Schema::dropIfExists('sliders');
    }
};
