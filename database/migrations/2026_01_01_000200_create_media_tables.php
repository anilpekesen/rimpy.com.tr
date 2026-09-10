<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('media_folders')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['parent_id', 'slug']);
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $table->string('disk')->default('public');
            $table->string('path');                      // orijinal dosyanın yolu
            $table->string('filename');                  // sunucudaki güvenli ad
            $table->string('original_name');             // kullanıcının yüklediği ad
            $table->string('type', 16)->index();         // image | document | video | archive
            $table->string('mime', 128);
            $table->string('extension', 16);
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            // Aynı dosyanın ikinci kez yüklenmesini yakalamak için içerik özeti.
            $table->string('hash', 64)->nullable()->index();

            // Üretilen boyutlar: {"thumb": {"path": "...", "w": 320, "h": 320}, ...}
            $table->json('variants')->nullable();

            // Dile göre alternatif metin ve başlık: {"tr": "...", "en": "..."}
            $table->json('alt')->nullable();
            $table->json('title')->nullable();
            $table->text('description')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('media_folders');
    }
};
