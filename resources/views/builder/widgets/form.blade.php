@php $locale = $context->locale; @endphp

@if (! $form)
    @if ($context->editing)
        <p class="text-sm text-ink-400">Form seçilmedi. Sağdaki panelden bir form seçin.</p>
    @endif
@else
    <div class="frm" data-form>
        @if (! empty($settings['title']))
            <h3 class="frm-title">{{ $settings['title'] }}</h3>
        @endif
        @if (! empty($settings['description']))
            <p class="frm-desc">{{ $settings['description'] }}</p>
        @endif

        <form method="POST" action="{{ route('form.submit', $form->key) }}" class="frm-form">
            @csrf
            <input type="hidden" name="locale" value="{{ $locale }}">
            <input type="hidden" name="page_url" value="{{ request()->fullUrl() }}">

            {{-- Bal tuzağı: gerçek ziyaretçi bu alanı görmez ve doldurmaz;
                 otomatik gönderim yapan botlar doldurur ve elenir. --}}
            @if ($form->spam_protection)
                <div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden">
                    <label>Bu alanı boş bırakın <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>
                <input type="hidden" name="_ts" value="{{ now()->timestamp }}">
            @endif

            <div class="frm-grid">
                @foreach ($form->fields as $field)
                    @php
                        $name = $field['key'];
                        $label = $field['label'][$locale] ?? $field['label'][\App\Support\Locales::default()] ?? $name;
                        $required = ! empty($field['required']);
                        $inputId = "f-{$form->key}-{$name}";
                        $full = ($field['width'] ?? 100) >= 100;
                    @endphp

                    <div class="frm-field @if ($full) frm-field--full @endif">
                        @if ($field['type'] === 'checkbox')
                            <label class="frm-check" for="{{ $inputId }}">
                                <input class="frm-input" type="checkbox" id="{{ $inputId }}" name="{{ $name }}"
                                       value="1" @required($required)>
                                <span>{{ $label }}@if ($required) <span aria-hidden="true">*</span>@endif</span>
                            </label>
                        @else
                            <label class="frm-label" for="{{ $inputId }}">
                                {{ $label }}@if ($required) <span aria-hidden="true">*</span>@endif
                            </label>

                            @switch ($field['type'])
                                @case('textarea')
                                    <textarea class="frm-input" id="{{ $inputId }}" name="{{ $name }}"
                                              rows="{{ $field['rows'] ?? 4 }}" @required($required)
                                              @if ($required) aria-required="true" @endif></textarea>
                                    @break

                                @case('select')
                                    <select class="frm-input" id="{{ $inputId }}" name="{{ $name }}" @required($required)>
                                        <option value="">—</option>
                                        @foreach ($field['options'] ?? [] as $option)
                                            <option value="{{ $option['value'] ?? $option }}">{{ $option['label'][$locale] ?? $option['value'] ?? $option }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('file')
                                    <input class="frm-input" type="file" id="{{ $inputId }}" name="{{ $name }}"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" @required($required)>
                                    @break

                                @default
                                    <input class="frm-input" type="{{ $field['type'] }}" id="{{ $inputId }}"
                                           name="{{ $name }}" @required($required)
                                           @if ($required) aria-required="true" @endif
                                           autocomplete="{{ ['email' => 'email', 'tel' => 'tel'][$field['type']] ?? 'on' }}">
                            @endswitch
                        @endif

                        @error($name, 'form-'.$form->key)
                            <p class="frm-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn-el frm-submit @if (($settings['button_width'] ?? 'auto') === 'full') btn-el--full @endif">
                {{ $form->localized('submit_label', $locale) ?? 'Gönder' }}
            </button>
        </form>

        @if (session('form_success') === $form->key)
            <p class="frm-success" role="status">{{ $form->localized('success_message', $locale) }}</p>
        @endif
    </div>
@endif
