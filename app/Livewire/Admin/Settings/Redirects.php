<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Redirect;
use App\Services\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Redirects extends Component
{
    use WithPagination;

    public string $from = '';

    public string $to = '';

    public int $statusCode = 301;

    public function add(): void
    {
        $this->validate([
            'from' => 'required|string|max:255|unique:redirects,from',
            'to' => 'required|string|max:255',
            'statusCode' => 'required|in:301,302',
        ], [
            'from.required' => 'Eski adresi girin.',
            'from.unique' => 'Bu adres için zaten bir yönlendirme var.',
            'to.required' => 'Yeni adresi girin.',
        ]);

        $redirect = Redirect::create([
            'from' => '/'.trim($this->from, '/'),
            'to' => str_starts_with($this->to, 'http') ? $this->to : '/'.trim($this->to, '/'),
            'status_code' => $this->statusCode,
        ]);

        ActivityLogger::created($redirect, "Yönlendirme eklendi: {$redirect->from} → {$redirect->to}");

        $this->reset('from', 'to');
        $this->dispatch('notify', message: 'Yönlendirme eklendi.');
    }

    public function delete(int $id): void
    {
        $redirect = Redirect::findOrFail($id);
        ActivityLogger::deleted($redirect, "Yönlendirme silindi: {$redirect->from}");
        $redirect->delete();
    }

    public function render()
    {
        return view('livewire.admin.settings.redirects', [
            'redirects' => Redirect::orderByDesc('hits')->paginate(30),
        ])->title('Yönlendirmeler');
    }
}
