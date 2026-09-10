@php
    $items = $settings['items'] ?? [];
    $cols = data_get($settings, 'columns.d.size', 3);
@endphp

@if ($items)
    <div class="cl tst-grid"
         style="grid-template-columns: repeat({{ ($settings['layout'] ?? 'grid') === 'grid' ? (int) $cols : 1 }}, minmax(0, 1fr))">
        @foreach ($items as $item)
            @php $avatar = $context->media($item['avatar'] ?? null); @endphp
            <figure class="tst">
                @if (! empty($item['rating']))
                    <div class="tst-stars" aria-label="{{ $item['rating'] }} / 5">
                        @for ($i = 0; $i < (int) $item['rating']; $i++)
                            <x-builder.icon name="star" style="fill:currentColor"/>
                        @endfor
                    </div>
                @endif

                <blockquote class="tst-quote">{{ $item['quote'] ?? '' }}</blockquote>

                <figcaption class="tst-foot">
                    @if ($avatar)
                        <img class="tst-avatar" src="{{ $avatar->url('thumb') }}" alt="" loading="lazy">
                    @endif
                    <span>
                        <span class="tst-name" style="display:block;font-weight:600">{{ $item['name'] ?? '' }}</span>
                        @if (! empty($item['role']))
                            <span class="tst-role" style="display:block;font-size:.875em;opacity:.7">{{ $item['role'] }}</span>
                        @endif
                    </span>
                </figcaption>
            </figure>
        @endforeach
    </div>
@endif
