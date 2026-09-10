<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class FormList extends Component
{
    public bool $creating = false;

    public string $newName = '';

    public function create()
    {
        $this->validate(
            ['newName' => 'required|string|max:120'],
            ['newName.required' => 'Forma bir ad verin.']
        );

        $key = Str::slug($this->newName) ?: 'form';
        $suffix = 1;

        while (Form::where('key', $key)->exists()) {
            $key = Str::slug($this->newName).'-'.(++$suffix);
        }

        $form = Form::create([
            'key' => $key,
            'name' => $this->newName,
            // Yeni form boş değil, çalışır hâlde gelir.
            'fields' => [
                ['key' => 'ad', 'type' => 'text', 'label' => Locales::map('Adınız Soyadınız'), 'required' => true, 'width' => 50],
                ['key' => 'eposta', 'type' => 'email', 'label' => Locales::map('E-posta'), 'required' => true, 'width' => 50],
                ['key' => 'mesaj', 'type' => 'textarea', 'label' => Locales::map('Mesajınız'), 'required' => true, 'width' => 100, 'rows' => 5],
            ],
            'submit_label' => Locales::map('Gönder'),
            'success_message' => Locales::map('Mesajınız bize ulaştı. En kısa sürede dönüş yapacağız.'),
            'notify_emails' => \App\Models\Setting::get('contact.email', config('mail.from.address')),
            'store_submissions' => true,
            'spam_protection' => true,
        ]);

        ActivityLogger::created($form, "Form oluşturuldu: {$form->name}");

        $this->reset('newName', 'creating');

        return $this->redirect(route('admin.forms.edit', $form), navigate: true);
    }

    public function delete(int $id): void
    {
        $form = Form::findOrFail($id);
        ActivityLogger::deleted($form, "Form silindi: {$form->name}");
        $form->delete();

        $this->dispatch('notify', message: 'Form ve ona gelen mesajlar silindi.');
    }

    public function render()
    {
        return view('livewire.admin.forms.form-list', [
            'forms' => Form::withCount([
                'submissions',
                'submissions as unread_count' => fn ($q) => $q->where('is_read', false)->where('is_spam', false),
            ])->orderBy('name')->get(),
        ])->title('Formlar');
    }
}
