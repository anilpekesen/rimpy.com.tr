<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         | Sayfalar
         | --------
         | Sayfanın kendisi dilden bağımsızdır: hiyerarşi, durum, şablon ve
         | yayın tarihi tüm dillerde ortaktır. Başlık, adres ve kurucu ağacı
         | ise page_translations içinde dile göre ayrı tutulur; böylece bir
         | müşteri Türkçe ana sayfayı İngilizceden tamamen farklı kurabilir.
         */
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('status', 16)->default('draft')->index();  // draft | published | scheduled
            $table->timestamp('published_at')->nullable()->index();
            $table->boolean('is_home')->default(false)->index();
            $table->boolean('show_in_search')->default(true);
            $table->string('visibility', 16)->default('public');      // public | password | private
            $table->string('password')->nullable();

            // Sayfaya özel üst/alt şablon. Boşsa site genelindeki kullanılır.
            $table->foreignId('header_template_id')->nullable();
            $table->foreignId('footer_template_id')->nullable();

            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 8)->index();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();

            // Kurucunun ürettiği eleman ağacı ve ondan derlenen CSS.
            // CSS'i saklıyoruz ki ön yüz her istekte yeniden hesaplamasın.
            $table->longText('builder_data')->nullable();
            $table->longText('compiled_css')->nullable();

            // Arama motoru alanları
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->foreignId('og_image_id')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('noindex')->default(false);

            $table->timestamps();
            $table->unique(['page_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        /*
         | Sürümler
         | --------
         | Her kayıtta kurucu ağacının o anki hali buraya kopyalanır. Müşteri
         | sayfayı bozarsa tek tıkla eski sürüme dönebilir.
         */
        Schema::create('page_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 8);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('builder_data')->nullable();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['page_id', 'locale', 'created_at']);
        });

        /*
         | İçerik tipleri
         | --------------
         | "Ürünler", "Haberler", "Referanslar", "Projeler"... Kullanıcı
         | panelden yeni bir tip tanımlar, sistem ona ait liste ekranını,
         | özel alanlarını ve ön yüz adreslerini otomatik üretir. Kod
         | yazılmadan yeni içerik türü açılabilmesinin yolu budur.
         */
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();                 // urunler, haberler
            $table->json('name');                            // {"tr": "Ürün", "en": "Product"}
            $table->json('plural');                          // {"tr": "Ürünler", ...}
            $table->string('icon', 64)->default('box');
            $table->boolean('is_hierarchical')->default(false);
            $table->boolean('has_archive')->default(true);
            $table->json('route_prefix')->nullable();        // {"tr": "urunler", "en": "products"}

            // Bu tipin hangi yetenekleri kullanacağı:
            // ["image", "gallery", "excerpt", "body", "builder", "seo", "taxonomies"]
            $table->json('supports')->nullable();

            // Kullanıcının tanımladığı ek alanlar:
            // [{"key":"fiyat","type":"number","label":{"tr":"Fiyat"},"required":false}, ...]
            $table->json('fields')->nullable();

            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);    // silinemeyen çekirdek tipler
            $table->timestamps();
        });

        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('content_items')->nullOnDelete();
            $table->string('status', 16)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('featured_image_id')->nullable();
            $table->json('gallery')->nullable();             // medya id listesi
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['content_type_id', 'status', 'published_at']);
        });

        Schema::create('content_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_item_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 8)->index();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->longText('builder_data')->nullable();
            $table->longText('compiled_css')->nullable();

            // İçerik tipinde tanımlanan ek alanların değerleri
            $table->json('custom')->nullable();

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('noindex')->default(false);

            $table->timestamps();
            $table->unique(['content_item_id', 'locale']);
            $table->index(['locale', 'slug']);
        });

        /*
         | Sınıflandırma
         | -------------
         | Bir içerik tipi istediği kadar sınıflandırma açabilir: ürünlerde
         | "Kategori" ve "Marka", haberlerde "Etiket" gibi.
         */
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->json('name');
            $table->json('plural');
            $table->boolean('is_hierarchical')->default(true);
            $table->json('route_prefix')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['content_type_id', 'key']);
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->foreignId('image_id')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('term_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 8)->index();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->unique(['term_id', 'locale']);
            $table->index(['locale', 'slug']);
        });

        Schema::create('content_item_term', function (Blueprint $table) {
            $table->foreignId('content_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->primary(['content_item_id', 'term_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_item_term');
        Schema::dropIfExists('term_translations');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('taxonomies');
        Schema::dropIfExists('content_item_translations');
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('content_types');
        Schema::dropIfExists('page_revisions');
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }
};
