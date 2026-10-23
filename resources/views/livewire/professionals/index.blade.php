<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Profissionais</h1>

    <form wire:submit="save" class="mb-8 grid grid-cols-1 sm:grid-cols-4 gap-4 items-start">
        <div>
            @if ($editingId)
                <p class="py-2 text-sm text-gray-600">Editando profissional</p>
            @else
                <select wire:model="user_id" class="w-full border-gray-300 rounded-md">
                    <option value="">Selecione um usuário</option>
                    @foreach ($availableUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <input type="text" wire:model="title" placeholder="Cargo (ex: Barbeiro)"
                   class="w-full border-gray-300 rounded-md">
            @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="text" wire:model="bio" placeholder="Bio (opcional)"
                   class="w-full border-gray-300 rounded-md">
            @error('bio') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="active" class="rounded border-gray-300">
                Ativo
            </label>

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
        </div>
        <div class="sm:col-span-4 mt-2 rounded-md border border-gray-200 bg-white p-4">
            <p class="text-sm font-medium text-gray-700 mb-3">Serviços que atende</p>

            <div class="flex flex-wrap gap-3">
                @forelse ($services as $service)
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">
                        <input type="checkbox" wire:model="service_ids" value="{{ $service->id }}"
                            class="rounded border-gray-300">
                        {{ $service->name }}
                    </label>
                @empty
                    <span class="text-sm text-gray-500">Cadastre serviços primeiro.</span>
                @endforelse
            </div>
        </div>
    </form>

    <div class="mb-4 flex items-center gap-2">
        <label for="filter" class="text-sm text-gray-600">Mostrar:</label>
        <select id="filter" wire:model.live="filter" class="border-gray-300 rounded-md text-sm">
            <option value="all">Todos</option>
            <option value="active">Ativos</option>
            <option value="inactive">Inativos</option>
        </select>
    </div>

    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Nome</th>
                <th class="p-2">E-mail</th>
                <th class="p-2">Cargo</th>
                <th class="p-2">Bio</th>
                <th class="p-2">Serviços</th>
                <th class="p-2">Ativo</th>
                <th class="p-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($professionals as $professional)
                <tr class="border-t">
                    <td class="p-2">{{ $professional->user->name }}</td>
                    <td class="p-2">{{ $professional->user->email }}</td>
                    <td class="p-2">{{ $professional->title ?? '-' }}</td>
                    <td class="p-2">{{ $professional->bio }}</td>
                    <td class="p-2">{{ $professional->services->pluck('name')->join(', ') ?: '-' }}</td>
                    <td class="p-2">{{ $professional->active ? 'Sim' : 'Não' }}</td>
                    <td class="p-2">
                        <div class="flex items-center gap-2">
                            <button wire:click="edit({{ $professional->id }})"
                                    class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-100">
                                Editar
                            </button>

                            <button wire:click="askDelete({{ $professional->id }})"
                                    class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 transition hover:bg-red-100">
                                Excluir
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="p-2">{{ $professional->services->pluck('name')->join(', ') ?: '-' }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <x-modal name="confirm-delete" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900">Excluir profissional</h2>
            <p class="mt-2 text-sm text-gray-600">
                Tem certeza que deseja excluir este profissional? O usuário volta a ser cliente.
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
