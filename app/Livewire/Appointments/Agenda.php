<?php

namespace App\Livewire\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class Agenda extends Component
{
    public string $date = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->professional, 403);

        $this->date = now()->toDateString();
    }

    public function previousDay(): void
    {
        $this->date = CarbonImmutable::parse($this->date)->subDay()->toDateString();
    }

    public function nextDay(): void
    {
        $this->date = CarbonImmutable::parse($this->date)->addDay()->toDateString();
    }

    public function render()
    {
        $professional = auth()->user()->professional;
        abort_unless($professional, 403);

        $day = CarbonImmutable::parse($this->date ?: now()->toDateString());

        $appointments = Appointment::with(['service', 'client'])
            ->where('professional_id', $professional->id)
            ->where('starts_at', '>=', $day->startOfDay())
            ->where('starts_at', '<=', $day->endOfDay())
            ->orderBy('starts_at')
            ->get();

        return view('livewire.appointments.agenda', [
            'appointments' => $appointments,
            'labels' => [
                AppointmentStatus::Pending->value => 'Pendente',
                AppointmentStatus::Confirmed->value => 'Confirmado',
                AppointmentStatus::Cancelled->value => 'Cancelado',
                AppointmentStatus::Completed->value => 'Concluído',
                AppointmentStatus::NoShow->value => 'Não compareceu',
            ],
        ]);
    }

    public function changeStatus(int $id, string $status): void
    {
        $professional = auth()->user()->professional;
        abort_unless($professional, 403);

        // Só mexe em agendamentos do próprio profissional
        $appointment = Appointment::where('professional_id', $professional->id)->findOrFail($id);

        $new = AppointmentStatus::tryFrom($status);
        abort_unless($new, 422);

        if (! $appointment->status->canChangeTo($new)) {
            $this->addError('status', 'Essa mudança de status não é permitida.');
            return;
        }

        // Concluir ou marcar falta só depois que o atendimento começou
        if (in_array($new, [AppointmentStatus::Completed, AppointmentStatus::NoShow], true)
            && $appointment->starts_at->isFuture()) {
            $this->addError('status', 'Só é possível concluir ou marcar falta depois do início do atendimento.');
            return;
        }

        $appointment->update(['status' => $new]);
        $this->resetValidation();
    }
}
