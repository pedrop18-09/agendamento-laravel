<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Disponibilidade</h1>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Profissional</label>
        <select wire:model.live="professional_id" class="w-full sm:w-72 border-gray-300 rounded-md">
            <option value="">Selecione um profissional</option>
            @foreach ($professionals as $p)
                <option value="{{ $p->id }}">{{ $p->user->name }}</option>
            @endforeach
        </select>
        @error('professional_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    @if ($professional_id)
        <form wire:submit="save" class="mb-8 grid grid-cols-1 sm:grid-cols-4 gap-6 items-start">
            <div>
                <select wire:model="day_of_week" class="w-full border-gray-300 rounded-md">
                    @foreach ($days as $number => $label)
                        <option value="{{ $number }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('day_of_week') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="time" wire:model="start_time" class="w-full border-gray-300 rounded-md">
                @error('start_time') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="time" wire:model="end_time" class="w-full border-gray-300 rounded-md">
                @error('end_time') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-gray-800 text-white rounded-md px-4 py-2">
                Adicionar
            </button>
        </form>

        <table class="w-full text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">Dia</th>
                    <th class="p-2">Início</th>
                    <th class="p-2">Fim</th>
                    <th class="p-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($availabilities as $a)
                    <tr class="border-t">
                        <td class="p-2">{{ $days[$a->day_of_week] }}</td>
                        <td class="p-2">{{ substr($a->start_time, 0, 5) }}</td>
                        <td class="p-2">{{ substr($a->end_time, 0, 5) }}</td>
                        <td class="p-2">
                            <button wire:click="delete({{ $a->id }})"
                                    class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 transition hover:bg-red-100">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-2 text-gray-500">Nenhum horário cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>
