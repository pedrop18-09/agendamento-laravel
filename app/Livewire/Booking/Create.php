<?php

namespace App\Livewire\Booking;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Service;
use App\Services\AvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Mail\AppointmentBooked;
use Illuminate\Support\Facades\Mail;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public ?int $service_id = null;
    public ?int $professional_id = null;
    public string $date = '';
    public string $selectedSlot = '';
    public string $notes = '';

    public function updatedServiceId(): void
    {
        $this->professional_id = null;
        $this->selectedSlot = '';
    }

    public function updatedProfessionalId(): void
    {
        $this->selectedSlot = '';
    }

    public function updatedDate(): void
    {
        $this->selectedSlot = '';
    }

    public function selectSlot(string $slot): void
    {
        $this->selectedSlot = $slot;
    }

    public function book(AvailabilityService $availability): void
    {
        $this->validate([
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'required|exists:professionals,id',
            'date' => 'required|date|after_or_equal:today',
            'selectedSlot' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ], [], [
            'service_id' => 'serviço',
            'professional_id' => 'profissional',
            'date' => 'data',
            'selectedSlot' => 'horário',
            'notes' => 'observações',
        ]);

        $service = Service::where('active', true)->findOrFail($this->service_id);
        $professional = Professional::where('active', true)
            ->whereHas('services', fn ($q) => $q->whereKey($service->id))
            ->findOrFail($this->professional_id);

        $start = CarbonImmutable::parse($this->date . ' ' . $this->selectedSlot);
        $end = $start->addMinutes($service->duration_minutes);

        $created = DB::transaction(function () use ($professional, $service, $start, $end, $availability) {
            // Trava a linha do profissional: quem chegar junto espera a transação terminar
            Professional::whereKey($professional->id)->lockForUpdate()->first();

            // Revalida dentro da transação: o horário ainda está livre?
            $free = $availability->slotsFor($professional, $service, $start->startOfDay());

            if (! in_array($start->format('H:i'), $free, true)) {
                return false;
            }

            $appointment = Appointment::create([
                'client_id' => auth()->id(),
                'professional_id' => $professional->id,
                'service_id' => $service->id,
                'starts_at' => $start,
                'ends_at' => $end,
                'status' => AppointmentStatus::Pending,
                'notes' => $this->notes ?: null,
            ]);

            return $appointment;
        });

        if (! $created) {
            $this->selectedSlot = '';
            $this->addError('selectedSlot', 'Esse horário acabou de ser ocupado. Escolha outro.');
            return;
        }

        Mail::to(auth()->user())->send(new AppointmentBooked($created));

        session()->flash('success', 'Agendamento realizado com sucesso!');
        $this->reset(['service_id', 'professional_id', 'date', 'selectedSlot', 'notes']);
    }

    public function render(AvailabilityService $availability)
    {
        $service = $this->service_id ? Service::find($this->service_id) : null;

        $professionals = $service
            ? $service->professionals()->where('active', true)->with('user')->get()
            : collect();

        $slots = [];

        if ($service && $this->professional_id && $this->date) {
            $professional = Professional::where('active', true)->find($this->professional_id);

            if ($professional) {
                $slots = $availability->slotsFor(
                    $professional,
                    $service,
                    CarbonImmutable::parse($this->date)
                );
            }
        }

        return view('livewire.booking.create', [
            'services' => Service::where('active', true)->orderBy('name')->get(),
            'professionals' => $professionals,
            'slots' => $slots,
        ]);
    }
}
