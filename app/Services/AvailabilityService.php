<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;

class AvailabilityService
{
    /**
     * Retorna os horários de início livres (ex: ['09:00', '09:30']).
     */
    public function slotsFor(Professional $professional, Service $service, CarbonImmutable $date): array
    {
        $duration = $service->duration_minutes;
        $now = CarbonImmutable::now();

        $windows = $professional->availabilities()
            ->where('day_of_week', $date->dayOfWeek)
            ->orderBy('start_time')
            ->get();

        // Busca os agendamentos do dia uma vez só (evita uma consulta por slot)
        $busy = $professional->appointments()
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->where('starts_at', '<', $date->endOfDay())
            ->where('ends_at', '>', $date->startOfDay())
            ->get(['starts_at', 'ends_at']);

        $timeOffs = $professional->timeOffs()
            ->where('starts_at', '<', $date->endOfDay())
            ->where('ends_at', '>', $date->startOfDay())
            ->get(['starts_at', 'ends_at']);

        $slots = [];

        foreach ($windows as $window) {
            $start = $date->setTimeFromTimeString($window->start_time);
            $end = $date->setTimeFromTimeString($window->end_time);

            $cursor = $start;

            while ($cursor->addMinutes($duration)->lte($end)) {
                $slotStart = $cursor;
                $slotEnd = $cursor->addMinutes($duration);

                $isPast = $slotStart->lte($now);

                $conflicts = $busy->contains(
                    fn ($a) => $a->starts_at->lt($slotEnd) && $a->ends_at->gt($slotStart)
                ) || $timeOffs->contains(
                    fn ($t) => $t->starts_at->lt($slotEnd) && $t->ends_at->gt($slotStart)
                );

                if (! $isPast && ! $conflicts) {
                    $slots[] = $slotStart->format('H:i');
                }

                $cursor = $slotEnd;
            }
        }

        return $slots;
    }
}
