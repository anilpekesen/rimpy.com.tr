<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Services\QuoteCart;
use App\Support\Locales;
use Illuminate\Http\Request;

/**
 * Teklif sepetinin uç noktaları.
 *
 * Ön yüzdeki betik bunları arka planda çağırır ve JSON alır. Betik
 * çalışmazsa aynı formlar normal biçimde gönderilir ve ziyaretçi geldiği
 * sayfaya döner; sepet yine çalışır, yalnızca sayfa yenilenir.
 */
class QuoteController extends Controller
{
    public function __construct(private QuoteCart $cart) {}

    public function show(Request $request)
    {
        return response()->json($this->cart->toArray($this->locale($request)));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'item' => 'required|integer',
            'quantity' => 'nullable|integer|min:1|max:999',
        ]);

        $item = ContentItem::with('type')->find($data['item']);

        abort_unless($item && $this->cart->accepts($item), 404);

        $this->cart->add($item->id, $data['quantity'] ?? 1);

        return $this->respond($request, 'added');
    }

    /**
     * Birden fazla ürünü tek istekte ekler: silindir seçicinin önerdiği
     * set (silindirler, pompa, hortum, manometre) gibi.
     */
    public function addMany(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|max:20',
            'items.*' => 'integer|min:1|max:999',
        ]);

        $items = ContentItem::with('type')->whereKey(array_keys($data['items']))->get();

        foreach ($items as $item) {
            if ($this->cart->accepts($item)) {
                $this->cart->add($item->id, (int) $data['items'][$item->id]);
            }
        }

        return $this->respond($request, 'added');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'item' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:999',
        ]);

        $this->cart->set($data['item'], $data['quantity']);

        return $this->respond($request, 'updated');
    }

    public function remove(Request $request)
    {
        $data = $request->validate(['item' => 'required|integer']);

        $this->cart->remove($data['item']);

        return $this->respond($request, 'removed');
    }

    private function respond(Request $request, string $status)
    {
        if ($request->expectsJson()) {
            return response()->json($this->cart->toArray($this->locale($request)) + ['status' => $status]);
        }

        return back()->with('quote_status', $status);
    }

    /** Sepet satırlarındaki başlık ve adresler ziyaretçinin dilinde gelsin. */
    private function locale(Request $request): string
    {
        $locale = (string) $request->input('locale', '');

        return Locales::exists($locale) ? $locale : Locales::default();
    }
}
