<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Serviços</h1>

    <form wire:submit="save" class="mb-8 grid grid-cols-1 sm:grid-cols-4 gap-4 items-start">
        <div>
            <input type="text" wire:model="name" placeholder="Nome do serviço"
                class="w-full border-gray-300 rounded-md">
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="number" wire:model="duration_minutes" placeholder="Minutos"
                class="w-full border-gray-300 rounded-md">
            @error('duration_minutes') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="number" step="0.01" wire:model="price" placeholder="Preço"
                class="w-full border-gray-300 rounded-md">
            @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-gray-800 text-white rounded-md px-4 py-2">
                {{ $editingId ? 'Atualizar' : 'Salvar' }}
            </button>

            @if ($editingId)
                <button type="button" wire:click="cancelEdit"
                        class="border border-gray-300 rounded-md px-4 py-2">
                    Cancelar
                </button>
            @endif
        </div>
    </form>

    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Nome</th>
                <th class="p-2">Duração</th>
                <th class="p-2">Preço</th>
                <th class="p-2">Ativo</th>
                <th class="p-2">Ações</th>
            </tr>
        </thead>
        <tbody>

            @forelse ($services as $service)
                <tr class="border-t">
                    <td class="p-2">{{ $service->name }}</td>
                    <td class="p-2">{{ $service->duration_minutes }} min</td>
                    <td class="p-2">R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                    <td class="p-2">{{ $service->active ? 'Sim' : 'Não' }}</td>
                    <td class="p-2">
                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $service->id }})"
                                class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                            </svg>
                            Editar
                        </button>

                        <button wire:click="askDelete({{ $service->id }})"
                                class="inline-flex items-center gap-1 rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 transition hover:bg-red-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            Excluir
                        </button>
                    </div>
                </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-2 text-gray-500">Nenhum serviço cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <x-modal name="confirm-delete" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900">Excluir serviço</h2>
            <p class="mt-2 text-sm text-gray-600">
                Tem certeza que deseja excluir este serviço? Essa ação não pode ser desfeita.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'confirm-delete')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="confirmDelete">
                    Excluir
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
