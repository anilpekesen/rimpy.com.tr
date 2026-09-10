@php $poster = $settings['poster'] ?? null; @endphp

<div class="video" @if ($poster) style="background-image:url('{{ $poster->url('large') }}');background-size:cover;background-position:center" @endif>
    @if (($settings['source'] ?? 'youtube') === 'file' && ($file = $settings['file'] ?? null))
        <video
            @if ($settings['controls'] ?? true) controls @endif
            @if ($settings['autoplay'] ?? false) autoplay @endif
            @if ($settings['muted'] ?? true) muted playsinline @endif
            @if ($settings['loop'] ?? false) loop @endif
            @if ($poster) poster="{{ $poster->url('large') }}" @endif
            preload="metadata">
            <source src="{{ $file->url() }}" type="{{ $file->mime }}">
        </video>
    @elseif ($embed)
        {{-- Video yalnızca kullanıcı oynat düğmesine bastığında yüklenir;
             sayfanın ilk açılışı üçüncü taraf betikleriyle yavaşlamaz. --}}
        <div x-data="{ playing: {{ ($settings['autoplay'] ?? false) ? 'true' : 'false' }} }" class="absolute inset-0">
            <template x-if="playing">
                <iframe src="{{ $embed }}" title="{{ $settings['title'] ?? 'Video' }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe>
            </template>
            <button type="button" x-show="!playing" @click="playing = true"
                    class="absolute inset-0 flex items-center justify-center"
                    style="background: rgba(0,0,0,.25)" aria-label="Videoyu oynat">
                <span style="display:flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:999px;background:rgba(255,255,255,.92);color:#111">
                    <x-builder.icon name="play" style="width:28px;height:28px"/>
                </span>
            </button>
        </div>
    @elseif ($context->editing)
        <div class="absolute inset-0 flex items-center justify-center text-sm text-white/70">
            Video adresi girilmedi ya da tanınmadı.
        </div>
    @endif
</div>
