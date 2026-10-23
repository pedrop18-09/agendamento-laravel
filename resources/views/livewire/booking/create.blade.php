<div class="max-w-2xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Agendar horário</h1>

    @if (session('success'))
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">1. Serviço</label>
            <select wire:model.live="service_id" class="w-full border-gray-300 rounded-md">
                <option value="">Selecione um serviço</option>
                @foreach ($services as $s)
                    <option value="{{ $s->id }}">
                        {{ $s->name }} ({{ $s->duration_minutes }} min, R$ {{ number_format($s->price, 2, ',', '.') }})
                    </option>
                @endforeach
            </select>
        </div>

        @if ($service_id)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">2. Profissional</label>
                <select wire:model.live="professional_id" class="w-full border-gray-300 rounded-md">
                    <option value="">Selecione um profissional</option>
                    @foreach ($professionals as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->user->name }}{{ $p->title ? ' - '.$p->title : '' }}
                        </option>
                    @endforeach
                </select>
                @if ($professionals->isEmpty())
                    <p class="text-sm text-gray-500 mt-1">Nenhum profissional atende este serviço.</p>
                @endif
            </div>
        @endif

        @if ($professional_id)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">3. Data</label>
                <input type="date" wire:model.live="date" min="{{ now()->toDateString() }}"
                       class="w-full border-gray-300 rounded-md">
            </div>
        @endif

        @if ($professional_id && $date)
            <div>
                <p class="block text-sm font-medium text-gray-700 mb-2">4. Horários livres</p>

                @forelse ($slots as $slot)
                    <button type="button" wire:click="selectSlot('{{ $slot }}')"
                            class="mr-2 mb-2 rounded-md border px-3 py-2 text-sm transition
                                   {{ $selectedSlot === $slot
                                       ? 'border-gray-800 bg-gray-800 text-white'
                                       : 'border-gray-300 hover:bg-gray-100' }}">
                        {{ $slot }}
                    </button>
                @empty
                    <p class="text-sm text-gray-500">Nenhum horário livre neste dia.</p>
                @endforelse

                @error('selectedSlot') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        @if ($selectedSlot)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações (opcional)</label>
                <textarea wire:model="notes" rows="2" class="w-full border-gray-300 rounded-md"></textarea>
                @error('notes') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="button" wire:click="book"
                    class="bg-gray-800 text-white rounded-md px-5 py-2">
                Confirmar agendamento às {{ $selectedSlot }}
            </button>
        @endif
    </div>
</div>
