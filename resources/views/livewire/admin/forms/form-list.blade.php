<div class="max-w-3xl space-y-4">

    <div class="flex items-center justify-between gap-3">
        <p class="text-sm text-ink-600">
            Form oluşturup sayfa kurucusundaki <strong>Form</strong> öğesiyle istediğiniz sayfaya yerleştirirsiniz.
        </p>
        <button wire:click="$set('creating', true)" class="btn-primary shrink-0">
            <x-icon name="plus" class="size-4"/> Yeni form
        </button>
    </div>

    @if ($creating)
        <form wire:submit="create" class="card flex flex-wrap items-start gap-3 p-4">
            <div class="min-w-56 flex-1">
                <label for="f-name" class="label">Form adı</label>
                <input id="f-name" type="text" wire:model="newName"
                       class="field @error('newName') field-error @enderror" placeholder="Teklif Formu" autofocus>
                @error('newName')
                    <p class="error">{{ $message }}</p>
                @else
                    <p class="hint">Ad, alanları hazır bir formla birlikte oluşturulur; sonra düzenlersiniz.</p>
                @enderror
            </div>
            <div class="flex gap-2 pt-6.5">
                <button type="submit" class="btn-primary">Oluştur</button>
                <button type="button" wire:click="$set('creating', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @endif

    @if ($forms->isEmpty())
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <x-icon name="clipboard" class="size-10 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">Henüz form yok.</p>
        </div>
    @else
        <ul class="card divide-y divide-ink-100">
            @foreach ($forms as $form)
                <li class="group flex items-center gap-3 px-4 py-3">
                    <x-icon name="clipboard" class="size-4 shrink-0 text-ink-400"/>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-ink-900">{{ $form->name }}</span>
                        <span class="block text-xs text-ink-400">
                            {{ $form->submissions_count }} mesaj · {{ count($form->fields) }} alan ·
                            <code class="font-mono">{{ $form->key }}</code>
                        </span>
                    </span>

                    @if ($form->unread_count > 0)
                        <a href="{{ route('admin.submissions.index', ['form' => $form->id]) }}" wire:navigate
                           class="badge-green shrink-0">{{ $form->unread_count }} yeni</a>
                    @endif

                    <div class="flex shrink-0 gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                        <a href="{{ route('admin.forms.edit', $form) }}" wire:navigate class="btn-ghost btn-sm">
                            <x-icon name="edit" class="size-4"/><span class="sr-only">Düzenle</span>
                        </a>
                        <a href="{{ route('admin.submissions.index', ['form' => $form->id]) }}" wire:navigate class="btn-ghost btn-sm">
                            <x-icon name="inbox" class="size-4"/><span class="sr-only">Mesajlar</span>
                        </a>
                        <button wire:click="delete({{ $form->id }})"
                                wire:confirm="“{{ $form->name }}” ve bu forma gelen {{ $form->submissions_count }} mesaj kalıcı olarak silinecek. Devam edilsin mi?"
                                class="btn-ghost btn-sm text-red-600 hover:bg-red-50">
                            <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
