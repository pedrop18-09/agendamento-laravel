<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Meus agendamentos</h1>

    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Data</th>
                <th class="p-2">Horário</th>
                <th class="p-2">Serviço</th>
                <th class="p-2">Profissional</th>
                <th class="p-2">Status</th>
                <th class="p-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $a)
                <tr class="border-t">
                    <td class="p-2">{{ $a->starts_at->format('d/m/Y') }}</td>
                    <td class="p-2">{{ $a->starts_at->format('H:i') }} - {{ $a->ends_at->format('H:i') }}</td>
                    <td class="p-2">{{ $a->service->name }}</td>
                    <td class="p-2">{{ $a->professional->user->name }}</td>
                    <td class="p-2">{{ $labels[$a->status->value] }}</td>
                    <td class="p-2">
                        @if ($canCancel($a))
                            <button wire:click="askCancel({{ $a->id }})"
                                    class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 transition hover:bg-red-100">
                                Cancelar
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-2 text-gray-500">Você ainda não tem agendamentos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <x-modal name="confirm-cancel" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900">Cancelar agendamento</h2>
            <p class="mt-2 text-sm text-gray-600">
                Tem certeza que deseja cancelar este agendamento? O horário ficará livre para outros clientes.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'confirm-cancel')">
                    Voltar
                </x-secondary-button>

                <x-danger-button wire:click="confirmCancel">
                    Cancelar agendamento
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
