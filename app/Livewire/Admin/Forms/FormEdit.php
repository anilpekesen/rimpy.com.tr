<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class FormEdit extends Component
{
    public Form $form;

    public array $fields = [];

    public array $settings = [];

    public const FIELD_TYPES = [
        'text' => 'Kısa metin',
        'email' => 'E-posta',
        'tel' => 'Telefon',
        'number' => 'Sayı',
        'textarea' => 'Uzun metin',
        'select' => 'Açılır liste',
        'checkbox' => 'Onay kutusu',
        'date' => 'Tarih',
        'file' => 'Dosya yükleme',
    ];

    public function mount(Form $form): void
    {
        $this->form = $form;
        $this->fields = $form->fields ?? [];

        $this->settings = [
            'name' => $form->name,
            'submit_label' => array_replace(Locales::map('Gönder'), $form->submit_label ?? []),
            'success_message' => array_replace(Locales::map(''), $form->success_message ?? []),
            'notify_emails' => $form->notify_emails ?? '',
            'redirect_url' => $form->redirect_url ?? '',
            'store_submissions' => $form->store_submissions,
            'spam_protection' => $form->spam_protection,
        ];
    }

    public function addField(string $type): void
    {
        $base = Str::slug(self::FIELD_TYPES[$type] ?? 'alan');
        $key = $base;
        $suffix = 1;

        // Alan anahtarları benzersiz olmalı; gelen mesajlarda değerler bu
        // anahtarla eşleşir.
        while (collect($this->fields)->contains('key', $key)) {
            $key = $base.'-'.(++$suffix);
        }

        $this->fields[] = [
            'key' => $key,
            'type' => $type,
            'label' => Locales::map(self::FIELD_TYPES[$type] ?? 'Alan'),
            'required' => false,
            'width' => $type === 'textarea' ? 100 : 50,
            'options' => $type === 'select' ? [['value' => 'secenek-1', 'label' => Locales::map('Seçenek 1')]] : [],
        ];
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function moveField(int $index, string $direction): void
    {
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if (! isset($this->fields[$target])) {
            return;
        }

        [$this->fields[$index], $this->fields[$target]] = [$this->fields[$target], $this->fields[$index]];
    }

    public function save(): void
    {
        $rules = [
            'settings.name' => 'required|string|max:120',
            'settings.notify_emails' => 'nullable|string|max:500',
            'settings.redirect_url' => 'nullable|string|max:500',
            'fields.*.key' => 'required|alpha_dash|max:40',
            'fields.*.type' => 'required|in:'.implode(',', array_keys(self::FIELD_TYPES)),
        ];

        $rules['fields.*.label.'.Locales::default()] = 'required|string|max:180';

        $this->validate($rules, [
            'settings.name.required' => 'Form adı zorunludur.',
            'fields.*.key.alpha_dash' => 'Alan anahtarı yalnızca harf, rakam ve tire içerebilir.',
            'fields.*.label.'.Locales::default().'.required' => 'Her alanın bir etiketi olmalı.',
        ]);

        // Bildirim adresleri gerçekten e-posta mı?
        $invalid = collect(explode(',', $this->settings['notify_emails']))
            ->map('trim')->filter()
            ->reject(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

        if ($invalid->isNotEmpty()) {
            $this->addError('settings.notify_emails',
                'Şu adresler geçerli değil: '.$invalid->implode(', '));

            return;
        }

        $this->form->update([
            'name' => $this->settings['name'],
            'fields' => array_values($this->fields),
            'submit_label' => $this->settings['submit_label'],
            'success_message' => $this->settings['success_message'],
            'notify_emails' => $this->settings['notify_emails'] ?: null,
            'redirect_url' => $this->settings['redirect_url'] ?: null,
            'store_submissions' => (bool) $this->settings['store_submissions'],
            'spam_protection' => (bool) $this->settings['spam_protection'],
        ]);

        ActivityLogger::updated($this->form, "Form güncellendi: {$this->form->name}");

        $this->dispatch('notify', message: 'Form kaydedildi.');
    }

    public function render()
    {
        return view('livewire.admin.forms.form-edit', [
            'fieldTypes' => self::FIELD_TYPES,
            'locales' => Locales::all(),
        ])->title($this->form->name);
    }
}
