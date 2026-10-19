@props(['name' => ''])
@php
/** Ön yüzde widget'ların kullandığı ikon seti. */
$paths = [
    'check' => '<path d="M20 6 9 17l-5-5"/>',
    'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/>',
    'star' => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21l1.2-6.9-5-4.9 6.9-1z"/>',
    'heart' => '<path d="M19 5a5 5 0 0 0-7 0l-.9.9-1-.9a5 5 0 0 0-7 7l8 8 8-8a5 5 0 0 0 0-7z"/>',
    'shield' => '<path d="M20 13c0 5-3.5 7.5-7.7 9a1 1 0 0 1-.6 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.2-2.7a1 1 0 0 1 1.5 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"/>',
    'award' => '<circle cx="12" cy="8" r="6"/><path d="m9 13.5-1 8 4-2 4 2-1-8"/>',
    /* Tırtıklı rozet: içinde onay işareti, altında iki kurdele ucu. */
    'badge-check' => '<path d="M10.93 0.99Q12 0 13.08 0.99Q14.15 1.98 15.58 1.66Q17 1.34 17.44 2.74Q17.87 4.13 19.27 4.56Q20.66 5 20.34 6.43Q20.02 7.85 21.01 8.93Q22 10 21.01 11.08Q20.02 12.15 20.34 13.58Q20.66 15 19.27 15.43Q17.87 15.87 17.44 17.27Q17 18.66 15.58 18.34Q14.15 18.02 13.08 19.01Q12 20 10.93 19.01Q9.85 18.02 8.43 18.34Q7 18.66 6.56 17.27Q6.13 15.87 4.73 15.43Q3.34 15 3.66 13.58Q3.98 12.15 2.99 11.08Q2 10 2.99 8.93Q3.98 7.85 3.66 6.43Q3.34 5 4.73 4.56Q6.13 4.13 6.56 2.74Q7 1.34 8.43 1.66Q9.85 1.98 10.93 0.99Z"/><circle cx="12" cy="10" r="5.1"/><path d="m9.7 10.1 1.7 1.7 3-3.2"/><path d="M8.5 17.9 6.4 24l5.6-2.4 5.6 2.4-2.1-6.1"/>',
    'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2 4.2 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
    'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/>',
    'map-pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
    'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    'truck' => '<path d="M14 17V5a1 1 0 0 0-1-1H2v12h1"/><path d="M14 8h4l4 4v5h-2"/><circle cx="6.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
    'package' => '<path d="M21 8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/>',
    'factory' => '<path d="M2 20h20M4 20V9l6 4V9l6 4V4h4v16"/>',
    'leaf' => '<path d="M11 20A7 7 0 0 1 4 13c0-6 5-11 16-11 0 11-5 16-11 16z"/><path d="M4 20c2-6 6-9 11-11"/>',
    'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/>',
    'trending-up' => '<path d="m22 7-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/>',
    'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
    'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1"/>',
    'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/>',
    'play' => '<path d="M6 4v16l14-8z"/>',
    'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
    'chevron-right' => '<path d="m9 6 6 6-6 6"/>',

    /* Sosyal ağlar. Bu işaretler çizgi değil dolgu ile çizilir; markaların
       kendi biçimleri çizgi hâlinde tanınmıyor. */
    'facebook' => '<path fill="currentColor" stroke="none" d="M14 9V7.5c0-.8.2-1.2 1.3-1.2H17V3.1A18 18 0 0 0 14.6 3C12 3 10.3 4.6 10.3 7.5V9H8v3.5h2.3V21H14v-8.5h2.5L17 9z"/>',
    'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1.2" fill="currentColor" stroke="none"/>',
    'linkedin' => '<path fill="currentColor" stroke="none" d="M6.9 8.5H3.6V21h3.3zM5.3 3a1.9 1.9 0 1 0 0 3.9 1.9 1.9 0 0 0 0-3.9M21 14.1c0-3.5-1.9-5.2-4.4-5.2-2 0-2.9 1.1-3.4 1.9V8.5H10V21h3.2v-6.6c0-1.5.7-2.4 2-2.4 1.2 0 1.7.9 1.7 2.4V21H21z"/>',
    'x-twitter' => '<path fill="currentColor" stroke="none" d="M17.5 3h3l-6.6 7.5L21.7 21h-6l-4.7-6.1L5.6 21h-3l7-8-6.8-10h6.2l4.3 5.6zm-1 16h1.6L7.6 4.7H5.8z"/>',
    'youtube' => '<path fill="currentColor" stroke="none" d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4a2.5 2.5 0 0 0-1.8 1.8C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8c1.6.4 7.8.4 7.8.4s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8M10 15V9l5.2 3z"/>',
    'whatsapp' => '<path fill="currentColor" stroke="none" d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2m0 1.9a8.1 8.1 0 1 1-4.1 15.1l-.3-.2-3 .8.8-3-.2-.3A8.1 8.1 0 0 1 12 3.9m-3.7 4c-.2 0-.5 0-.7.4-.3.3-.9.9-.9 2.1s.9 2.4 1 2.6c.2.2 1.8 2.8 4.4 3.8 2.2.9 2.6.7 3.1.7s1.5-.6 1.7-1.2c.2-.7.2-1.2.2-1.3l-.6-.3-1.5-.8c-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.6 6.6 0 0 1-3.3-2.9c-.1-.2 0-.4.1-.5l.4-.5.3-.5v-.5l-.8-1.9c-.2-.4-.4-.4-.6-.4z"/>',
];
@endphp
@if ($name && isset($paths[$name]))
    <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor',
        'stroke-width' => '1.75', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round',
        'width' => '1em', 'height' => '1em', 'aria-hidden' => 'true']) }}>{!! $paths[$name] !!}</svg>
@endif
