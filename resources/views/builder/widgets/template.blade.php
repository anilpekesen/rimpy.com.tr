@if ($template)
    {!! app(\App\Builder\Renderer::class)->template($template, $context) !!}
@elseif ($context->editing)
    <p class="text-sm text-ink-400">Kayıtlı bölüm seçilmedi.</p>
@endif
