<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Submissions extends Component
{
    use WithPagination;

    #[Url(as: 'form')]
    public ?int $formId = null;

    #[Url(as: 'filtre')]
    public string $filter = 'inbox';

    public ?int $openId = null;

    public function open(int $id): void
    {
        $this->openId = $id;

        $submission = FormSubmission::find($id);

        if ($submission && ! $submission->is_read) {
            $submission->update(['is_read' => true]);
        }
    }

    public function markSpam(int $id): void
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->update(['is_spam' => ! $submission->is_spam, 'is_read' => true]);

        $this->openId = null;
        $this->dispatch('notify', message: $submission->is_spam ? 'İstenmeyen olarak işaretlendi.' : 'Gelen kutusuna taşındı.');
    }

    public function markAllRead(): void
    {
        FormSubmission::where('is_read', false)
            ->when($this->formId, fn ($q) => $q->where('form_id', $this->formId))
            ->update(['is_read' => true]);

        $this->dispatch('notify', message: 'Tümü okundu olarak işaretlendi.');
    }

    public function delete(int $id): void
    {
        $submission = FormSubmission::findOrFail($id);
        ActivityLogger::deleted($submission, 'Form mesajı silindi');
        $submission->delete();

        $this->openId = null;
        $this->dispatch('notify', message: 'Mesaj silindi.');
    }

    /** Gelen mesajları tablo olarak dışa aktarır. */
    public function export()
    {
        $rows = FormSubmission::with('form')
            ->when($this->formId, fn ($q) => $q->where('form_id', $this->formId))
            ->where('is_spam', false)
            ->latest()->get();

        // Anahtarlar mesajlar arasında değişebildiği için başlık satırı
        // tüm kayıtların birleşiminden üretilir.
        $columns = $rows->flatMap(fn ($r) => array_keys($r->data))->unique()->values();

        $filename = 'mesajlar-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($rows, $columns) {
            $out = fopen('php://output', 'w');

            // Excel'in Türkçe karakterleri doğru okuması için bayt işareti.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Tarih', 'Form', ...$columns->all()], ';');

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->created_at->format('d.m.Y H:i'),
                    $row->form?->name,
                    ...$columns->map(fn ($c) => (string) ($row->data[$c] ?? ''))->all(),
                ], ';');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $submissions = FormSubmission::with('form')
            ->when($this->formId, fn ($q) => $q->where('form_id', $this->formId))
            ->when($this->filter === 'inbox', fn ($q) => $q->where('is_spam', false))
            ->when($this->filter === 'unread', fn ($q) => $q->where('is_read', false)->where('is_spam', false))
            ->when($this->filter === 'spam', fn ($q) => $q->where('is_spam', true))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.forms.submissions', [
            'submissions' => $submissions,
            'forms' => Form::orderBy('name')->get(),
            'open' => $this->openId ? FormSubmission::with('form')->find($this->openId) : null,
            'counts' => [
                'inbox' => FormSubmission::where('is_spam', false)->count(),
                'unread' => FormSubmission::where('is_read', false)->where('is_spam', false)->count(),
                'spam' => FormSubmission::where('is_spam', true)->count(),
            ],
        ])->title('Gelen mesajlar');
    }
}
