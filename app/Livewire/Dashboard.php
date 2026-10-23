<?php

namespace App\Livewire;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $active = [AppointmentStatus::Pending, AppointmentStatus::Confirmed];

        $nextAppointment = Appointment::with(['service', 'professional.user'])
            ->where('client_id', $user->id)
            ->whereIn('status', $active)
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->first();

        $todayCount = $user->professional
            ? Appointment::where('professional_id', $user->professional->id)
                ->whereIn('status', $active)
                ->whereDate('starts_at', today())
                ->count()
            : null;

        $adminStats = $user->isAdmin()
            ? [
                'services' => Service::where('active', true)->count(),
                'professionals' => Professional::where('active', true)->count(),
                'upcoming' => Appointment::whereIn('status', $active)
                    ->where('starts_at', '>', now())
                    ->count(),
            ]
            : null;

        return view('livewire.dashboard', compact('nextAppointment', 'todayCount', 'adminStats'));
    }
}
