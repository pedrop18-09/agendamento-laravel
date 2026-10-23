<?php

namespace App\Livewire\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class Mine extends Component
{
    public ?int $cancellingId = null;

    public function render()
    {
        $appointments = Appointment::with(['service', 'professional.user'])
            ->where('client_id', auth()->id())
            ->orderByDesc('starts_at')
            ->get();

        return view('livewire.appointments.mine', [
            'appointments' => $appointments,
            'labels' => [
                AppointmentStatus::Pending->value => 'Pendente',
                AppointmentStatus::Confirmed->value => 'Confirmado',
                AppointmentStatus::Cancelled->value => 'Cancelado',
                AppointmentStatus::Completed->value => 'Concluído',
                AppointmentStatus::NoShow->value => 'Não compareceu',
            ],
            'canCancel' => fn (Appointment $a) => Gate::allows('cancel', $a),
        ]);
    }

    public function askCancel(int $id): void
    {
        $appointment = Appointment::where('client_id', auth()->id())->findOrFail($id);

        Gate::authorize('cancel', $appointment);

        $this->cancellingId = $appointment->id;
        $this->dispatch('open-modal', 'confirm-cancel');
    }

    public function confirmCancel(): void
    {
        $appointment = Appointment::where('client_id', auth()->id())->findOrFail($this->cancellingId);

        Gate::authorize('cancel', $appointment);

        $appointment->update(['status' => AppointmentStatus::Cancelled]);

        $this->cancellingId = null;
        $this->dispatch('close-modal', 'confirm-cancel');
    }
}
