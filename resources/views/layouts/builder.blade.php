<!DOCTYPE html>
<html lang="tr" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Tasarım' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($fontUrl = app(\App\Support\Theme::class)->fontUrl())
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="{{ $fontUrl }}">
    @endif
    @livewireStyles
    <style>{!! app(\App\Support\Theme::class)->cssVariables() !!}</style>
</head>
<body class="h-full overflow-hidden bg-ink-200">
    {{ $slot }}
    @livewireScripts
</body>
</html>
