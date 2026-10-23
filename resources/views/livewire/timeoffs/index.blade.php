<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Minhas folgas</h1>

    <form wire:submit="save" class="mb-8 grid grid-cols-1 sm:grid-cols-4 gap-6 items-start">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Início</label>
            <input type="datetime-local" wire:model="starts_at" class="w-full border-gray-300 rounded-md">
            @error('starts_at') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Fim</label>
            <input type="datetime-local" wire:model="ends_at" class="w-full border-gray-300 rounded-md">
            @error('ends_at') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Motivo (opcional)</label>
            <input type="text" wire:model="reason" placeholder="Ex: férias" class="w-full border-gray-300 rounded-md">
            @error('reason') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:pt-6">
            <button type="submit" class="bg-gray-800 text-white rounded-md px-4 py-2">Adicionar</button>
        </div>
    </form>

    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Início</th>
                <th class="p-2">Fim</th>
                <th class="p-2">Motivo</th>
                <th class="p-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($timeOffs as $t)
                <tr class="border-t">
                    <td class="p-2">{{ $t->starts_at->format('d/m/Y H:i') }}</td>
                    <td class="p-2">{{ $t->ends_at->format('d/m/Y H:i') }}</td>
                    <td class="p-2">{{ $t->reason ?? '-' }}</td>
                    <td class="p-2">
                        <button wire:click="delete({{ $t->id }})"
                                class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 transition hover:bg-red-100">
                            Excluir
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-2 text-gray-500">Nenhuma folga cadastrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
