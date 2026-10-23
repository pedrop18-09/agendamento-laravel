<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Minha agenda</h1>

    <div class="mb-6 flex items-center gap-3">
        <button wire:click="previousDay"
                class="rounded-md border border-gray-300 px-3 py-2 text-sm hover:bg-gray-100">
            &larr; Anterior
        </button>

        <input type="date" wire:model.live="date" class="border-gray-300 rounded-md">

        <button wire:click="nextDay"
                class="rounded-md border border-gray-300 px-3 py-2 text-sm hover:bg-gray-100">
            Próximo &rarr;
        </button>
    </div>
    @error('status')
    <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ $message }}
    </div>
    @enderror
    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Horário</th>
                <th class="p-2">Cliente</th>
                <th class="p-2">Serviço</th>
                <th class="p-2">Observações</th>
                <th class="p-2">Status</th>
                <th class="p-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $a)
                <tr class="border-t">
                    <td class="p-2">{{ $a->starts_at->format('H:i') }} - {{ $a->ends_at->format('H:i') }}</td>
                    <td class="p-2">{{ $a->client->name }}</td>
                    <td class="p-2">{{ $a->service->name }}</td>
                    <td class="p-2">{{ $a->notes ?? '-' }}</td>
                    <td class="p-2">{{ $labels[$a->status->value] }}</td>
                    <td class="p-2">
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($a->status->allowedTransitions() as $next)
                                @php
                                    $style = match ($next) {
                                        \App\Enums\AppointmentStatus::Confirmed => 'border-green-200 bg-green-50 text-green-700 hover:bg-green-100',
                                        \App\Enums\AppointmentStatus::Completed => 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100',
                                        default => 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100',
                                    };
                                    $text = [
                                        'confirmed' => 'Confirmar',
                                        'completed' => 'Concluir',
                                        'no_show' => 'Não compareceu',
                                        'cancelled' => 'Cancelar',
                                    ][$next->value];
                                @endphp

                                <button wire:click="changeStatus({{ $a->id }}, '{{ $next->value }}')"
                                        class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm font-medium transition {{ $style }}">
                                    {{ $text }}
                                </button>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-2 text-gray-500">Nenhum atendimento neste dia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
