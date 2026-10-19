@php
    $current = '/'.ltrim(request()->path(), '/');
    $prefix = $context->prefix();
@endphp

@if (! $menu)
    @if ($context->editing)
        <p class="text-sm text-ink-400">Menü seçilmedi.</p>
    @endif
@else
    <nav class="nav-wrap" aria-label="{{ $menu->name }}"
         data-menu data-mobile="{{ $settings['mobile'] ?? 'drawer' }}"
         data-breakpoint="{{ $settings['breakpoint'] ?? 'tablet' }}">

        <button class="nav-toggle" data-menu-toggle aria-expanded="false" aria-label="Menüyü aç">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav @if (($settings['orientation'] ?? 'horizontal') === 'vertical') nav--vertical @endif
                   @if ($indicator = $settings['indicator'] ?? null) nav--{{ $indicator }} @endif"
            data-menu-list>
            @foreach ($menu->tree as $item)
                @php $href = $item->href($context->locale); @endphp
                @continue (! $href)

                <li class="nav-item-el @if ($item->activeChildren->isNotEmpty()) nav-item-el--has-sub @endif @if ($item->is_mega && $item->activeChildren->isNotEmpty()) nav-item-el--mega @endif">
                    <a class="nav-link @if (rtrim($href, '/') === rtrim($current, '/')) is-active @endif"
                       href="{{ $href }}" target="{{ $item->target }}"
                       @if ($item->target === '_blank') rel="noopener" @endif
                       @if (rtrim($href, '/') === rtrim($current, '/')) aria-current="page" @endif>
                        {{ $item->title($context->locale) }}
                        @if ($item->activeChildren->isNotEmpty())
                            <x-builder.icon name="chevron-down"/>
                        @endif
                    </a>

                    @if ($item->is_mega && $item->activeChildren->isNotEmpty())
                        {{-- Mega menü: her alt öğe görselli bir sütun, torunları
                             sütunun listesi. Görsel bağlı kategorinin görselidir. --}}
                        <div class="nav-sub nav-mega">
                            <div class="nav-mega-grid">
                                @foreach ($item->activeChildren as $column)
                                    @php
                                        $columnHref = $column->href($context->locale);
                                        $image = $column->image();
                                    @endphp
                                    @continue (! $columnHref)
                                    <div class="nav-mega-col">
                                        <a class="nav-mega-media" href="{{ $columnHref }}" tabindex="-1" aria-hidden="true">
                                            @if ($image)
                                                <img src="{{ $image->url('small') }}" alt="" loading="lazy" decoding="async">
                                            @endif
                                        </a>
                                        <a class="nav-link nav-mega-title" href="{{ $columnHref }}">{{ $column->title($context->locale) }}</a>
                                        @if ($column->activeChildren->isNotEmpty())
                                            <ul class="nav-mega-list">
                                                @foreach ($column->activeChildren as $leaf)
                                                    @php $leafHref = $leaf->href($context->locale); @endphp
                                                    @continue (! $leafHref)
                                                    <li><a class="nav-link" href="{{ $leafHref }}">{{ $leaf->title($context->locale) }}</a></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($item->activeChildren->isNotEmpty())
                        <ul class="nav-sub">
                            @foreach ($item->activeChildren as $child)
                                @php $childHref = $child->href($context->locale); @endphp
                                @continue (! $childHref)
                                <li>
                                    <a class="nav-link" href="{{ $childHref }}" target="{{ $child->target }}"
                                       @if ($child->target === '_blank') rel="noopener" @endif>
                                        {{ $child->title($context->locale) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif
