@php $items = $settings['items'] ?? []; @endphp

@if ($items)
    <div class="tabs tabs--{{ $settings['orientation'] ?? 'horizontal' }}" data-tabs>
        <div class="tabs-list" role="tablist">
            @foreach ($items as $index => $item)
                <button class="tab-btn" role="tab" type="button"
                        id="tab-{{ $element->id }}-{{ $index }}"
                        aria-controls="panel-{{ $element->id }}-{{ $index }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        tabindex="{{ $index === 0 ? '0' : '-1' }}">
                    @if (! empty($item['icon'])) <x-builder.icon :name="$item['icon']"/> @endif
                    {{ $item['title'] ?? '' }}
                </button>
            @endforeach
        </div>

        @foreach ($items as $index => $item)
            <div class="tab-panel tx" role="tabpanel"
                 id="panel-{{ $element->id }}-{{ $index }}"
                 aria-labelledby="tab-{{ $element->id }}-{{ $index }}"
                 @if ($index !== 0) hidden @endif>
                {!! \App\Support\Html::sanitize($item['body'] ?? '') !!}
            </div>
        @endforeach
    </div>
@endif
