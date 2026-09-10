<?php

namespace App\Livewire\Admin\Design;

use App\Models\Template;
use App\Services\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
class TemplateList extends Component
{
    #[Url(as: 'tur')]
    public string $type = 'section';

    public bool $creating = false;

    public string $newName = '';

    public const TYPES = [
        'section' => ['label' => 'Bölümler', 'hint' => 'Sayfalarda tekrar kullandığınız hazır bölümler.'],
        'header' => ['label' => 'Üst başlık', 'hint' => 'Sitenin her sayfasının en üstünde görünen alan.'],
        'footer' => ['label' => 'Alt bilgi', 'hint' => 'Sitenin her sayfasının en altında görünen alan.'],
        'popup' => ['label' => 'Açılır pencere', 'hint' => 'Duyuru ya da çerez bildirimi gibi pencereler.'],
    ];

    public function create()
    {
        $this->validate(
            ['newName' => 'required|string|max:120'],
            ['newName.required' => 'Bir ad girin.']
        );

        $template = Template::create([
            'name' => $this->newName,
            'type' => $this->type,
            'author_id' => auth()->id(),
            // Aynı türden ilk şablon otomatik olarak varsayılan olur.
            'is_default' => in_array($this->type, ['header', 'footer'], true)
                && ! Template::where('type', $this->type)->where('is_default', true)->exists(),
        ]);

        ActivityLogger::created($template, "Şablon oluşturuldu: {$this->newName}");

        $this->reset('newName', 'creating');

        return $this->redirect(route('admin.builder.template', $template), navigate: false);
    }

    public function setDefault(int $id): void
    {
        $template = Template::findOrFail($id);

        Template::where('type', $template->type)->update(['is_default' => false]);
        $template->update(['is_default' => true]);

        ActivityLogger::updated($template, "Varsayılan {$template->type}: {$template->name}");
        $this->dispatch('notify', message: 'Varsayılan şablon güncellendi.');
    }

    public function duplicate(int $id): void
    {
        $original = Template::findOrFail($id);

        $copy = $original->replicate(['is_default']);
        $copy->name = $original->name.' (kopya)';
        $copy->is_default = false;
        $copy->author_id = auth()->id();
        $copy->save();

        $this->dispatch('notify', message: 'Şablon kopyalandı.');
    }

    public function delete(int $id): void
    {
        $template = Template::findOrFail($id);

        if ($template->is_default) {
            $this->dispatch('notify', message: 'Varsayılan şablon silinemez. Önce başka bir şablonu varsayılan yapın.');

            return;
        }

        ActivityLogger::deleted($template, "Şablon silindi: {$template->name}");
        $template->delete();

        $this->dispatch('notify', message: 'Şablon silindi.');
    }

    public function render()
    {
        return view('livewire.admin.design.template-list', [
            'templates' => Template::where('type', $this->type)->latest()->get(),
            'types' => self::TYPES,
        ])->title('Şablonlar');
    }
}
