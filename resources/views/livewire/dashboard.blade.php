<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    <h1 class="text-2xl font-bold">Olá, {{ auth()->user()->name }}!</h1>

    <div class="rounded-md border border-gray-200 bg-white p-6">
        <h2 class="text-sm font-medium text-gray-500 mb-3">Seu próximo agendamento</h2>

        @if ($nextAppointment)
            <p class="text-lg font-semibold">{{ $nextAppointment->service->name }}</p>
            <p class="text-gray-600">
                {{ $nextAppointment->starts_at->format('d/m/Y') }} às {{ $nextAppointment->starts_at->format('H:i') }}
                com {{ $nextAppointment->professional->user->name }}
            </p>
            <a href="{{ route('appointments.mine') }}" wire:navigate
               class="mt-3 inline-block text-sm text-blue-600 hover:underline">Ver meus agendamentos</a>
        @else
            <p class="text-gray-600">Você não tem agendamentos futuros.</p>
            <a href="{{ route('booking.create') }}" wire:navigate
               class="mt-3 inline-block rounded-md bg-gray-800 px-4 py-2 text-sm text-white">
                Agendar horário
            </a>
        @endif
    </div>

    @if ($todayCount !== null)
        <div class="rounded-md border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-medium text-gray-500 mb-1">Atendimentos hoje</h2>
            <p class="text-3xl font-bold">{{ $todayCount }}</p>
            <a href="{{ route('appointments.agenda') }}" wire:navigate
               class="mt-2 inline-block text-sm text-blue-600 hover:underline">Abrir minha agenda</a>
        </div>
    @endif

    @if ($adminStats)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-md border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-medium text-gray-500 mb-1">Serviços ativos</h2>
                <p class="text-3xl font-bold">{{ $adminStats['services'] }}</p>
            </div>
            <div class="rounded-md border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-medium text-gray-500 mb-1">Profissionais ativos</h2>
                <p class="text-3xl font-bold">{{ $adminStats['professionals'] }}</p>
            </div>
            <div class="rounded-md border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-medium text-gray-500 mb-1">Agendamentos futuros</h2>
                <p class="text-3xl font-bold">{{ $adminStats['upcoming'] }}</p>
            </div>
        </div>
    @endif
</div>
