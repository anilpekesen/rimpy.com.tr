{{--
    Yalnızca yönetici ve üzeri roller bu öğeyi düzenleyebilir; içerik
    bilinçli olarak olduğu gibi basılır (harici gömme kodları çalışsın diye).
--}}
{!! \App\Support\Html::raw($settings['code'] ?? '') !!}
