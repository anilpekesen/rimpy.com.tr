<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();       // header, footer, mobile
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();

            // Nereye gidiyor: home | page | content | term | archive | url
            $table->string('type', 16)->default('page');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('url')->nullable();

            $table->json('label');                 // {"tr": "Hakkımızda", "en": "About"}
            $table->string('target', 8)->default('_self');
            $table->string('icon', 64)->nullable();
            $table->string('css_class')->nullable();
            $table->boolean('is_mega')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /*
         | Şablonlar
         | ---------
         | Kurucuda hazırlanan her şey buraya kaydedilebilir: tekrar
         | kullanılacak bir bölüm, tüm siteye uygulanan bir üst başlık ya da
         | bir açılır pencere. "Global" işaretlenen şablon değiştiğinde onu
         | kullanan bütün sayfalar aynı anda güncellenir.
         */
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 16)->default('section')->index(); // section | page | header | footer | popup
            $table->longText('builder_data')->nullable();
            $table->longText('compiled_css')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('category')->nullable();     // kütüphanede gruplamak için
            $table->boolean('is_global')->default(false);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_starter')->default(false); // ürünle gelen hazır şablon
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /*
         | Formlar
         | -------
         | Kullanıcı panelden alanlarını sürükleyerek form kurar, kurucuda
         | "Form" widget'ını sayfaya yerleştirir ve gelen mesajları panelde
         | okur. Kod yazılması gereken tek şey yoktur.
         */
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');

            // [{"key":"ad","type":"text","label":{"tr":"Adınız"},"required":true,"width":50}, ...]
            $table->json('fields');

            $table->json('submit_label')->nullable();
            $table->json('success_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->text('notify_emails')->nullable();     // virgülle ayrılmış
            $table->boolean('store_submissions')->default(true);
            $table->boolean('send_autoreply')->default(false);
            $table->json('autoreply_subject')->nullable();
            $table->longText('autoreply_body')->nullable();
            $table->boolean('spam_protection')->default(true);
            $table->timestamps();
        });

        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->json('data');
            $table->string('page_url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('locale', 8)->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->boolean('is_spam')->default(false)->index();
            $table->timestamps();
        });

        /*
         | Yönlendirmeler
         | --------------
         | Eski siteden taşınırken kırılan adresleri kurtarmak için. Arama
         | motorlarındaki sıralamayı korumanın en ucuz yolu.
         */
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from')->unique();
            $table->string('to');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /*
         | Arayüz metinleri
         | ----------------
         | Ön yüzdeki sabit ifadeler ("Devamını oku", "Gönder"...). Müşteri
         | bunları da kendi değiştirebilsin diye veritabanında tutuluyor.
         */
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('site')->index();
            $table->string('key');
            $table->json('values');                 // {"tr": "Devamını oku", "en": "Read more"}
            $table->timestamps();
            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
