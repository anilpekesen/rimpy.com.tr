<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Teklif sepeti: ekleme, adet, kaldırma, yalnızca yayındaki ürünlerin
 * kabulü ve teklif formu gönderildiğinde ürünlerin mesaja eklenmesi.
 */
class QuoteCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        Form::create([
            'key' => config('cms.quote.form_key'),
            'name' => 'Teklif',
            'fields' => [
                ['key' => 'ad', 'type' => 'text', 'label' => ['tr' => 'Ad'], 'required' => true],
                ['key' => 'eposta', 'type' => 'email', 'label' => ['tr' => 'E-posta'], 'required' => true],
            ],
            'success_message' => ['tr' => 'Alındı'],
            'store_submissions' => true,
            'spam_protection' => false,
        ]);
    }

    private function product(string $code, string $status = 'published'): ContentItem
    {
        $item = ContentItem::create([
            'content_type_id' => ContentType::where('key', 'urunler')->value('id'),
            'status' => $status,
            'published_at' => now()->subMinute(),
        ]);

        $item->fillTranslation('tr', [
            'title' => "Ürün {$code}",
            'slug' => strtolower($code),
            'custom' => ['model_kodu' => $code],
        ]);

        return $item;
    }

    public function test_urun_eklenir_adet_degisir_ve_kaldirilir(): void
    {
        $item = $this->product('RSC-1');

        $this->postJson(route('quote.add'), ['item' => $item->id, 'quantity' => 2])
            ->assertOk()->assertJsonPath('count', 2)->assertJsonPath('lines.0.code', 'RSC-1');

        $this->postJson(route('quote.add'), ['item' => $item->id])->assertJsonPath('count', 3);
        $this->postJson(route('quote.update'), ['item' => $item->id, 'quantity' => 5])->assertJsonPath('count', 5);
        $this->postJson(route('quote.remove'), ['item' => $item->id])->assertJsonPath('count', 0);
    }

    public function test_yayinda_olmayan_ya_da_baska_turdeki_kayit_eklenemez(): void
    {
        $draft = $this->product('TASLAK', 'draft');

        $this->postJson(route('quote.add'), ['item' => $draft->id])->assertNotFound();
        $this->postJson(route('quote.add'), ['item' => 999999])->assertNotFound();
        $this->getJson(route('quote.show'))->assertJsonPath('count', 0);
    }

    public function test_adet_sinirlanir(): void
    {
        $item = $this->product('RSC-2');

        $this->postJson(route('quote.add'), ['item' => $item->id, 'quantity' => 999]);
        $this->postJson(route('quote.add'), ['item' => $item->id, 'quantity' => 999])
            ->assertJsonPath('count', config('cms.quote.max_quantity'));
    }

    public function test_teklif_formu_sepeti_mesaja_ekler_ve_bosaltir(): void
    {
        $item = $this->product('RHT-08');
        $this->postJson(route('quote.add'), ['item' => $item->id, 'quantity' => 3]);

        $this->postJson(route('form.submit', config('cms.quote.form_key')), [
            'ad' => 'Deneme', 'eposta' => 'deneme@example.com', 'locale' => 'tr',
        ])->assertOk()->assertJsonPath('status', 'sent');

        $submission = FormSubmission::latest('id')->first();
        $this->assertSame('3 × RHT-08 — Ürün RHT-08', $submission->data['urunler']);
        $this->assertSame('Talep edilen ürünler', $submission->form->fieldLabel('urunler'));

        $this->getJson(route('quote.show'))->assertJsonPath('count', 0);
    }

    public function test_diger_formlar_sepete_dokunmaz(): void
    {
        $item = $this->product('RHT-09');
        $this->postJson(route('quote.add'), ['item' => $item->id]);

        $this->post(route('form.submit', 'iletisim'), [
            'ad' => 'Deneme', 'eposta' => 'deneme@example.com', 'mesaj' => 'Merhaba', 'onay' => '1',
        ]);

        $this->assertArrayNotHasKey('urunler', FormSubmission::latest('id')->first()->data);
        $this->getJson(route('quote.show'))->assertJsonPath('count', 1);
    }
}
