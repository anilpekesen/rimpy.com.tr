@php
    $separator = ['chevron' => '›', 'slash' => '/', 'dash' => '—', 'dot' => '·'][$settings['separator'] ?? 'chevron'];
    $trail = \App\Support\Breadcrumbs::for($context->subject, $context->locale, $settings['home_label'] ?: null);
@endphp

@if (count($trail) > 1)
    <nav aria-label="Yol izi">
        <ol class="crumbs">
            @foreach ($trail as $index => $crumb)
                @php $isLast = $index === count($trail) - 1; @endphp
                @if ($isLast && ! ($settings['show_current'] ?? true)) @continue @endif

                <li class="crumb @if ($isLast) is-current @endif">
                    @if ($isLast || ! $crumb['url'])
                        <span @if ($isLast) aria-current="page" @endif>{{ $crumb['label'] }}</span>
                    @else
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    @endif
                </li>

                @unless ($isLast)
                    <li class="crumb-sep" aria-hidden="true">{{ $separator }}</li>
                @endunless
            @endforeach
        </ol>
    </nav>
@endif
