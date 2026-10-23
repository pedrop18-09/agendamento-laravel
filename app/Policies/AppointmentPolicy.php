<?php

namespace App\Policies;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function cancel(User $user, Appointment $appointment): bool
    {
        $isOwner = $appointment->client_id === $user->id;

        if (! $isOwner && ! $user->isAdmin()) {
            return false;
        }

        return $this->isCancellable($appointment);
    }

    /**
     * Regra de negócio isolada, pra reaproveitar na tela.
     */
    public function isCancellable(Appointment $appointment): bool
    {
        $activeStatus = in_array($appointment->status, [
            AppointmentStatus::Pending,
            AppointmentStatus::Confirmed,
        ], true);

        return $activeStatus && $appointment->starts_at->gt(now()->addHours(2));
    }
}
