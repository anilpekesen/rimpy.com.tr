@props(['locale', 'context'])

@php
    use App\Models\Translation;

    $form = \App\Models\Form::where('key', config('cms.quote.form_key'))->first();
    $cart = app(\App\Services\QuoteCart::class);
    $state = $form ? $cart->toArray($locale) : null;
    $tr = fn (string $key, string $fallback) => Translation::get($key, $fallback, $locale);
@endphp

@if ($form)
    {{-- Teklif sepeti. Sayfada her zaman bulunur ama kapalı durur; açan
         düğmeler `data-quote-open` taşır. Ürün listesi betik tarafından
         güncellenir, ilk hâli sunucudan gelir ki betik yüklenmeden de dolu
         görünsün. --}}
    <div class="qc" data-quote
         data-endpoint-update="{{ route('quote.update') }}"
         data-endpoint-remove="{{ route('quote.remove') }}"
         data-locale="{{ $locale }}"
         data-text-added="{{ $tr('quote_added', 'teklif sepetine eklendi') }}"
         data-text-view="{{ $tr('quote_view', 'Sepeti gör') }}"
         data-text-remove="{{ $tr('quote_remove', 'Kaldır') }}"
         data-text-less="{{ $tr('quote_less', 'Azalt') }}"
         data-text-more="{{ $tr('quote_more', 'Arttır') }}"
         data-text-in-cart="{{ $tr('quote_in_cart', 'Sepette') }}"
         hidden>
        <div class="qc-scrim" data-quote-close></div>

        <aside class="qc-panel" role="dialog" aria-modal="true" aria-labelledby="qc-title">
            <header class="qc-head">
                <h2 id="qc-title" class="qc-title">{{ $tr('quote_cart', 'Teklif sepeti') }}</h2>
                <button type="button" class="qc-close" data-quote-close aria-label="{{ $tr('close', 'Kapat') }}">
                    <x-icon name="x" />
                </button>
            </header>

            <div class="qc-body">
                <ul class="qc-lines" data-quote-lines>
                    @foreach ($state['lines'] as $line)
                        <li class="qc-line" data-id="{{ $line['key'] }}">
                            <a class="qc-thumb" href="{{ $line['url'] }}">
                                @if ($line['image'])<img src="{{ $line['image'] }}" alt="" loading="lazy">@endif
                            </a>
                            <div class="qc-info">
                                <a class="qc-name" href="{{ $line['url'] }}">{{ $line['title'] }}</a>
                                @if ($line['code'] || $line['option'])<span class="qc-code">{{ implode(' · ', array_filter([$line['code'], $line['option']])) }}</span>@endif
                                <button type="button" class="qc-remove" data-quote-remove="{{ $line['key'] }}">{{ $tr('quote_remove', 'Kaldır') }}</button>
                            </div>
                            <div class="qc-qty">
                                <button type="button" data-quote-step="-1" aria-label="{{ $tr('quote_less', 'Azalt') }}">−</button>
                                <span aria-live="polite">{{ $line['quantity'] }}</span>
                                <button type="button" data-quote-step="1" aria-label="{{ $tr('quote_more', 'Arttır') }}">+</button>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p class="qc-empty" data-quote-empty @if ($state['lines']) hidden @endif>
                    {{ $tr('quote_empty', 'Sepetiniz boş. Ürünleri ekleyebilir ya da yalnızca ihtiyacınızı yazıp gönderebilirsiniz.') }}
                </p>
            </div>

            <div class="qc-form" data-quote-form>
                @include('builder.widgets.form', [
                    'form' => $form,
                    'context' => $context,
                    'settings' => ['button_width' => 'full'],
                ])
            </div>
        </aside>
    </div>

    <div class="qc-toast" data-quote-toast role="status" aria-live="polite" hidden>
        <span class="qc-toast-mark"><x-icon name="check" /></span>
        <span data-quote-toast-text></span>
        <button type="button" data-quote-open>{{ $tr('quote_view', 'Sepeti gör') }}</button>
    </div>
@endif
