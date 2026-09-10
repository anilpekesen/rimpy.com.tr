<div class="max-w-4xl space-y-4">

    <div class="flex flex-wrap items-center gap-2">
        <div class="flex rounded-lg border border-ink-200 bg-white p-0.5 text-sm">
            @foreach ($types as $key => $meta)
                <button wire:click="$set('type', '{{ $key }}')"
                        class="rounded-md px-3 py-1.5 font-medium transition
                               {{ $type === $key ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-100' }}">
                    {{ $meta['label'] }}
                </button>
            @endforeach
        </div>

        <button wire:click="$set('creating', true)" class="btn-primary ml-auto">
            <x-icon name="plus" class="size-4"/> Yeni şablon
        </button>
    </div>

    <div class="card-pad">
        <p class="text-sm text-ink-600">{{ $types[$type]['hint'] }}</p>
        @if (in_array($type, ['header', 'footer'], true))
            <p class="mt-1.5 text-xs text-ink-500">
                Varsayılan olarak işaretlenen şablon sitenin bütün sayfalarında görünür.
            </p>
        @endif
    </div>

    @if ($creating)
        <form wire:submit="create" class="card flex flex-wrap items-start gap-3 p-4">
            <div class="min-w-56 flex-1">
                <label for="tpl-name" class="label">Şablon adı</label>
                <input id="tpl-name" type="text" wire:model="newName"
                       class="field @error('newName') field-error @enderror" autofocus>
                @error('newName') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 pt-6.5">
                <button type="submit" class="btn-primary">Oluştur ve tasarla</button>
                <button type="button" wire:click="$set('creating', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @endif

    @if ($templates->isEmpty())
        <div class="card flex flex-col items-center justify-center py-14 text-center">
            <x-icon name="blocks" class="size-9 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">Bu türde şablon yok.</p>
            <button wire:click="$set('creating', true)" class="btn-primary mt-4">
                <x-icon name="plus" class="size-4"/> İlkini oluştur
            </button>
        </div>
    @else
        <ul class="card divide-y divide-ink-100">
            @foreach ($templates as $template)
                <li class="group flex items-center gap-3 px-4 py-3">
                    <x-icon name="blocks" class="size-4 shrink-0 text-ink-400"/>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-ink-900">{{ $template->name }}</span>
                        <span class="block text-xs text-ink-400">
                            {{ $template->updated_at?->diffForHumans() }}
                            @if ($template->author) · {{ $template->author->name }} @endif
                        </span>
                    </span>

                    @if ($template->is_default)
                        <span class="badge-green shrink-0">Varsayılan</span>
                    @endif

                    <div class="flex shrink-0 gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                        <a href="{{ route('admin.builder.template', $template) }}" class="btn-ghost btn-sm">
                            <x-icon name="edit" class="size-4"/><span class="sr-only">Tasarla</span>
                        </a>
                        @unless ($template->is_default)
                            @if (in_array($type, ['header', 'footer'], true))
                                <button wire:click="setDefault({{ $template->id }})" class="btn-ghost btn-sm">Varsayılan yap</button>
                            @endif
                        @endunless
                        <button wire:click="duplicate({{ $template->id }})" class="btn-ghost btn-sm">
                            <x-icon name="blocks" class="size-4"/><span class="sr-only">Kopyala</span>
                        </button>
                        @unless ($template->is_default)
                            <button wire:click="delete({{ $template->id }})"
                                    wire:confirm="“{{ $template->name }}” silinecek. Devam edilsin mi?"
                                    class="btn-ghost btn-sm text-red-600 hover:bg-red-50">
                                <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                            </button>
                        @endunless
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
