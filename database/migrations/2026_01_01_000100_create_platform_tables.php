<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sitenin konuştuğu diller. Panelden eklenip çıkarılabilir.
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('name');
            $table->string('native_name');
            $table->string('direction', 3)->default('ltr');
            $table->string('flag', 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // Genel ayarlar: gruplanmış anahtar/değer. Değerler JSON olduğu için
        // dile göre değişen ayarlar da (ör. site sloganı) burada durabilir.
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();
            $table->string('key');
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->unique(['group', 'key']);
        });

        // Kim, ne zaman, neyi değiştirdi. Bir sorun olduğunda geriye dönüp
        // bakabilmek için her yazma işlemi buraya düşer.
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event')->index();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['subject_type', 'subject_id']);
        });

        // Başarılı ve başarısız tüm giriş denemeleri. Kilitleme mantığı bu
        // tabloyu okur, panel de "şüpheli hareket" uyarısını buradan üretir.
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('ip_address', 45)->index();
            $table->boolean('successful')->default(false);
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('languages');
    }
};
